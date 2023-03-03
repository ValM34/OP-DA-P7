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
  ])// @TODO Demander à Laurent pourquoi quand j'enlève ces infos le JWT se génère quand même lorsque je lance les tests
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
    $client->request('GET', '/api/vendor');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $responseDataLength = count($responseData);
    for($i = 0; $i < $responseDataLength; $i++){
      $this->assertArrayHasKey('id', $responseData[$i]);
      $this->assertArrayHasKey('name', $responseData[$i]);
      $this->assertArrayHasKey('surname', $responseData[$i]);
      $this->assertArrayHasKey('email', $responseData[$i]);
      $this->assertArrayHasKey('updatedAt', $responseData[$i]);
      $this->assertArrayHasKey('createdAt', $responseData[$i]);
      $this->assertArrayHasKey('_links', $responseData[$i]);
      $this->assertArrayHasKey('customer', $responseData[$i]['_links']);
      $this->assertArrayHasKey('href', $responseData[$i]['_links']['customer']);
      $this->assertArrayHasKey('vendor', $responseData[$i]['_links']);
      $this->assertArrayHasKey('href', $responseData[$i]['_links']['vendor']);
    }

    // @TODO comprendre pourquoi ça marche pas
    // Lien vers les tutos : https://symfony.com/doc/current/the-fast-track/fr/17-tests.html, https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/3-functional-testing.html
    // $this->assertSelectorTextContains('h2', 'Give your feedback');
  }

  public function getCustomerId() // @TODO : Améliorer nom + sert pour route testGetCustomerByVendor & testDelete
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/vendor?page=1&limit=1');
    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
    $responseData = json_decode($client->getResponse()->getContent(), true);

    return $responseData[0]['id'];
  }

  public function testGetCustomerByVendor()
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
    $this->assertArrayHaskey('customer', $responseData['_links']);
    $this->assertArrayHaskey('href', $responseData['_links']['customer']);
    $this->assertArrayHaskey('vendor', $responseData['_links']);
    $this->assertArrayHaskey('href', $responseData['_links']['vendor']);
  }

  public function testCreate()
  {
    $client = $this->createAuthenticatedClient();
    $data = [ // @TODO : voir comment je peux changer l'email à chaque test. Pour l'instant je peux faire en sorte de supprimer l'élément nouvellement créé dans ma route delete mais je pense que c'est pas une bonne pratique
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
    $this->assertArrayHaskey('self', $responseData['_links']);
    $this->assertArrayHaskey('href', $responseData['_links']['self']);
    $this->assertArrayHaskey('vendor', $responseData['_links']);
    $this->assertArrayHaskey('href', $responseData['_links']['vendor']);
  }

  public function testDelete()
  {
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/customer/delete' . $this->getCustomerId());
    $this->assertResponseIsSuccessful();
    // @TODO : Voir si ce test est suffisant
  }
}

// @TODO : Si mes données de test ne lient aucun customer à mon vendor, mon test va forcément échouer, il faut donc que j'adapte
// la manière dont je créée mes données de test
// Faire les tests de create et delete
