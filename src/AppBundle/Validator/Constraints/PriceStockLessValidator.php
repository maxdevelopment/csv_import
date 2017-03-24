<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PriceStockLessValidator
 *
 * @package AppBundle\Validator\Constraints
 */
class PriceStockLessValidator extends ConstraintValidator
{
    /**
     * Validate product by stock&price
     *
     * @param Product    $product    Validating product
     * @param Constraint $constraint
     */
    public function validate($product, Constraint $constraint)
    {
        if ($product->getPrice() < $constraint->minPrice &&
            $product->getStock() < $constraint->minStock
        ) {
            $this->context->addViolation($constraint->message, array('%minPrice%' => $constraint->minPrice));
            $this->context->addViolation($constraint->message, array('%minStock%' => $constraint->minStock));
        }
    }
}