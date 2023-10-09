<?php
namespace App\Service;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FlashMessageHelper implements FlashMessageHelperInterface
{

public function __construct(
    public RequestStack $requestStack,
){}

public function addFormErrorsAsFlash(FormInterface $form) : void
{
    $errors = $form->getErrors(true);
    //Ajouts des erreurs du formulaire comme messages flash de la catégorie "error".
    $flashBag = $this->requestStack->getSession()->getFlashBag();
    foreach ($errors as $error) {
        $errorMsg = $error->getMessage();
        $flashBag->add('error', $errorMsg);
    }
}
}