<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

// Lancer les tests : symfony php bin/phpunit
class ProductControllerTest extends WebTestCase
{
  protected static function createAuthenticatedClient(array $claims = [
    'username' => 'e@mail0.fr',
    'password' => 'password',
    'roles' => ['ROLE_USER']
  ])
  {
    self::ensureKernelShutdown(); // Ca permet de utiliser plusieurs fois la fonction $client = self::createClient();
    $client = self::createClient();
    $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
    $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $encoder->encode($claims)));

    return $client;
  }

  public function testGetAll()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/product/all');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $responseDataLength = count($responseData['items']);
    for($i = 0; $i < $responseDataLength; $i++){
      $this->assertArrayHasKey('id', $responseData['items'][$i]);
      $this->assertArrayHasKey('name', $responseData['items'][$i]);
      $this->assertArrayHasKey('description', $responseData['items'][$i]);
      $this->assertArrayHasKey('price', $responseData['items'][$i]);
      $this->assertArrayHasKey('updatedAt', $responseData['items'][$i]);
      $this->assertArrayHasKey('createdAt', $responseData['items'][$i]);
      $this->assertArrayHasKey('_links', $responseData['items'][$i]);
    }
  }

  public function getProductId()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/product/all');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);

    return $responseData['items'][0]['id'];
  }

  public function testGetOne()
  {
    $productId = $this->getProductId();
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/product/' . $productId);
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHaskey('id', $responseData);
    $this->assertArrayHaskey('name', $responseData);
    $this->assertArrayHaskey('description', $responseData);
    $this->assertArrayHaskey('price', $responseData);
    $this->assertArrayHaskey('updatedAt', $responseData);
    $this->assertArrayHaskey('createdAt', $responseData);
    $this->assertArrayHaskey('_links', $responseData);
  }
}
