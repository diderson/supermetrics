<?php
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase {

	public function testPostJasonFileExist(): void{
		$this->assertFileExists('temp_posts.json');
	}
}