<?php

namespace App\Security;

use App\Entity\Customer;
use App\Entity\Vendor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CustomerVoter extends Voter
{
  const VIEW = 'view';

  protected function supports(string $attribute, mixed $subject): bool
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::VIEW])) {
      return false;
    }
    
    // only vote on `Customer` objects
    if (!$subject instanceof Customer) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $vendor = $token->getUser();

    if (!$vendor instanceof Vendor) {
      // the user must be logged in; if not, deny access
      return false;
    }

    // you know $subject is a Customer object, thanks to `supports()`
    /** @var Customer $customer */
    $customer = $subject;

    return match ($attribute) {
      self::VIEW => $this->canView($customer, $vendor),
      default => throw new \LogicException('This code should not be reached!')
    };
  }

  private function canView(Customer $customer, Vendor $vendor): bool
  {
    return $vendor === $customer->getVendor();
  }
}
