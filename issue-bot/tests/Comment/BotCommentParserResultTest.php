<?php declare(strict_types = 1);

namespace PHPStan\IssueBot\Comment;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Parser\MarkdownParser;
use PHPUnit\Framework\TestCase;

class BotCommentParserResultTest extends TestCase
{

	/**
	 * @return iterable<array{string, string, string}>
	 */
	public function dataParse(): iterable
	{
		yield [
			'@foobar After [the latest commit to dev-master](https://github.com/phpstan/phpstan-src/commit/abc123), PHPStan now reports different result with your [code snippet](https://phpstan.org/r/74c3b0af-5a87-47e7-907a-9ea6fbb1c396):

```diff
@@ @@
-1: abc
+1: def
```',
			'74c3b0af-5a87-47e7-907a-9ea6fbb1c396',
			'@@ @@
-1: abc
+1: def
',
		];
	}

	/**
	 * @dataProvider dataParse
	 */
	public function testParse(string $text, string $expectedHash, string $expectedDiff): void
	{
		$markdownEnvironment = new Environment();
		$markdownEnvironment->addExtension(new CommonMarkCoreExtension());
		$markdownEnvironment->addExtension(new GithubFlavoredMarkdownExtension());
		$parser = new BotCommentParser(new MarkdownParser($markdownEnvironment));
		$result = $parser->parse($text);
		self::assertSame($expectedHash, $result->getHash());
		self::assertSame($expectedDiff, $result->getDiff());
	}

}
