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
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('{{ minPrice }}', $constraint->minPrice)
//                ->setParameter('{{ minStock }}', $constraint->minStock)
//                ->addViolation();


            $this->context->addViolation($constraint->message, array('%minPrice%' => $constraint->minPrice));
            $this->context->addViolation($constraint->message, array('%minStock%' => $constraint->minStock));
        }



//        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
//            // If you're using the new 2.5 validation API (you probably are!)
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('%string%', $value)
//                ->addViolation();
//
//            // If you're using the old 2.4 validation API
//            /*
//            $this->context->addViolation(
//                $constraint->message,
//                array('%string%' => $value)
//            );
//            */
//        }
    }
}