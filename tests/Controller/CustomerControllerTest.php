<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

// Lancer les tests : symfony php bin/phpunit
// Les methods ayant pour début de nom "test" seront jouées lors des tests, pas les autres
class CustomerControllerTest extends WebTestCase
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

  public function testGetCustomersByVendor()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/customer/all');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $responseDataLength = count($responseData['items']);
    for($i = 0; $i < $responseDataLength; $i++){
      $this->assertArrayHasKey('id', $responseData['items'][$i]);
      $this->assertArrayHasKey('name', $responseData['items'][$i]);
      $this->assertArrayHasKey('surname', $responseData['items'][$i]);
      $this->assertArrayHasKey('email', $responseData['items'][$i]);
      $this->assertArrayHasKey('updatedAt', $responseData['items'][$i]);
      $this->assertArrayHasKey('createdAt', $responseData['items'][$i]);
      $this->assertArrayHasKey('_links', $responseData['items'][$i]);
      $this->assertArrayHasKey('customer', $responseData['items'][$i]['_links']);
      $this->assertArrayHasKey('href', $responseData['items'][$i]['_links']['customer']);
      $this->assertArrayHasKey('vendor', $responseData['items'][$i]['_links']);
      $this->assertArrayHasKey('href', $responseData['items'][$i]['_links']['vendor']);
    }
  }

  public function getCustomerId()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/customer/all?page=1&limit=1');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);

    return $responseData['items'][0]['id'];
  }

  public function testGetCustomer()
  {
    $customerId = $this->getCustomerId();
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/customer/' . $customerId);
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHaskey('id', $responseData);
    $this->assertArrayHaskey('name', $responseData);
    $this->assertArrayHaskey('surname', $responseData);
    $this->assertArrayHaskey('email', $responseData);
    $this->assertArrayHaskey('updatedAt', $responseData);
    $this->assertArrayHaskey('createdAt', $responseData);
    $this->assertArrayHaskey('_links', $responseData);
  }

  public function testCreate()
  {
    $client = $this->createAuthenticatedClient();
    $data = [
      'email' => 'email@newUser12.fr',
      'name' => 'valentin',
      'surname' => 'moreau'
    ];
    $client->request(
      'POST',
      '/api/customer/add',
      [],
      [],
      ['CONTENT_TYPE' => 'application/json'],
      json_encode($data)
    );
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHaskey('id', $responseData);
    $this->assertArrayHaskey('name', $responseData);
    $this->assertArrayHaskey('surname', $responseData);
    $this->assertArrayHaskey('email', $responseData);
    $this->assertArrayHaskey('updatedAt', $responseData);
    $this->assertArrayHaskey('createdAt', $responseData);
    $this->assertArrayHaskey('vendor', $responseData);
    $this->assertArrayHaskey('id', $responseData['vendor']);
    $this->assertArrayHaskey('name', $responseData['vendor']);
    $this->assertArrayHaskey('email', $responseData['vendor']);
    $this->assertArrayHaskey('roles', $responseData['vendor']);
    $this->assertArrayHaskey(0, $responseData['vendor']['roles']);
    $this->assertArrayHaskey('updatedAt', $responseData['vendor']);
    $this->assertArrayHaskey('createdAt', $responseData['vendor']);
    $this->assertArrayHaskey('_links', $responseData);
  }

  public function testDelete()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('DELETE', '/api/customer/delete' . $this->getCustomerId());
    $this->assertResponseIsSuccessful();
  }
}
