<?php

namespace CafCommon;

use Zend\Form\Form,
    Zend\Di\Di,
    Zend\InputFilter\InputFilter;

abstract class AbstractForm extends Form
{

    public function loadInputFilter($filtersNamespace = 'Application\Filter') {

        $parts = explode('\\', static::class);
        $class = "$filtersNamespace\\" . end($parts);

        try {
            $filter = (new Di)->get($class);
        } catch (\Exception $e) {
            $filter = new InputFilter;
        }

        $this->setInputFilter($filter);
    }

    public function validate()
    {
        if (!$this->isValid()) {
            throw new \RunTimeException($this->getExceptionMessage());
        }

        return true;
    }

    private function getExceptionMessage()
    {
        $errors  = $this->getInputFilter()->getMessages();
        $messages = array();

        foreach ($errors as $error) {
            $messages[] = array_shift($error);
        }

        return implode('; ', $messages);

    }

}
