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

    return match ($attribute) {
      self::VIEW => $this->canView($subject, $vendor),
      default => throw new \LogicException('This code should not be reached!')
    };
  }

  private function canView(Customer $customer, Vendor $vendor): bool
  {
    return $vendor->getId() === $customer->getVendor()->getId();
  }
}
