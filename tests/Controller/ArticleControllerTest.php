<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/recent-articles');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Recent articles');
    }

    public function testCommentForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/1');


        $buttonCrawlerNode = $crawler->selectButton('comment_submit');
        $form = $buttonCrawlerNode->form();
        $form->setValues(['comment[content]' => 'Je suis le test']);
        $client->submit($form);
        
        $crawler = $client->followRedirect();
        $this->assertEquals('Je suis le test', $crawler->filter("#comments li")->first()->text());
    }
}
