<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecondEmailTest extends WebTestCase
{
    public function testSucess(): void
    {

        $client = static::createClient();
        $urlGen = self::getContainer()->get(UrlGeneratorInterface::class);
        $userRepo = self::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        $client->loginUser($user);

        $crawler = $client->request('GET', '/room/secondEmail/change');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.modal-header', 'second.email.title');
        $buttonCrawlerNode = $crawler->selectButton('Speichern');
        $form = $buttonCrawlerNode->form();
        $form['second_email[secondEmail]'] = 'testcc@test.de, test@cc.de';

        $client->submit($form);
        $this->assertResponseRedirects($urlGen->generate('dashboard',array('snack'=>'CC-E-Mails erfolgreich geändert auf: testcc@test.de, test@cc.de')));
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        self::assertEquals('testcc@test.de, test@cc.de',$user->getSecondEmail());

    }
    public function testInvalidEmail(): void
    {

        $client = static::createClient();
        $urlGen = self::getContainer()->get(UrlGeneratorInterface::class);
        $userRepo = self::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        $client->loginUser($user);

        $crawler = $client->request('GET', '/room/secondEmail/change');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.modal-header', 'second.email.title');
        $buttonCrawlerNode = $crawler->selectButton('Speichern');
        $form = $buttonCrawlerNode->form();
        $form['second_email[secondEmail]'] = 'testcc@test, test@cc.de';

        $client->submit($form);
        $this->assertResponseRedirects($urlGen->generate('dashboard',array('snack'=>'Ungültige E-Mail. Bitte überprüfen Sie Ihre E-Mail-Adresse.','color'=>'danger')));
        self::assertEquals(null,$user->getSecondEmail());
    }
    public function testCombined(): void
    {

        $client = static::createClient();
        $urlGen = self::getContainer()->get(UrlGeneratorInterface::class);
        $userRepo = self::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        $client->loginUser($user);
        $crawler = $client->request('GET', '/room/secondEmail/change');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.modal-header', 'second.email.title');
        $buttonCrawlerNode = $crawler->selectButton('Speichern');
        $form = $buttonCrawlerNode->form();
        $form['second_email[secondEmail]'] = 'testcc@test.de, test@cc.de';

        $client->submit($form);
        $this->assertResponseRedirects($urlGen->generate('dashboard',array('snack'=>'CC-E-Mails erfolgreich geändert auf: testcc@test.de, test@cc.de')));
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        self::assertEquals('testcc@test.de, test@cc.de',$user->getSecondEmail());

        $crawler = $client->request('GET', '/room/secondEmail/change');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.modal-header', 'second.email.title');
        $buttonCrawlerNode = $crawler->selectButton('Speichern');
        $form = $buttonCrawlerNode->form();
        $form['second_email[secondEmail]'] = 'testcc@test, test@cc.de';

        $client->submit($form);
        $this->assertResponseRedirects($urlGen->generate('dashboard',array('snack'=>'Ungültige E-Mail. Bitte überprüfen Sie Ihre E-Mail-Adresse.','color'=>'danger')));
        $user = $userRepo->findOneBy(array('email'=>'test@local.de'));
        self::assertEquals('testcc@test.de, test@cc.de',$user->getSecondEmail());
    }
}
