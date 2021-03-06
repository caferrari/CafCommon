<?php

namespace CafCommon;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\ServiceManager\Exception\ServiceNotFoundException;

abstract class AbstractController extends AbstractActionController
{

    protected $entityManager = null;
    protected $service;
    protected $entity;
    protected $form;
    protected $controller;

    protected $messages = array(
        'success' => array(
            'insert' => 'Success Insert message not defined',
            'edit' => 'Success Edit message not defined',
            'delete' => 'Success Delete message not defined'
        ),
        'error' => array(
            'insert' => 'Insert Error message not defined',
            'edit' => 'Edit Error message not defined',
            'delete' => 'Delete Error message not defined',
            'unique' => 'Unique Error message not defined'
        )
    );

    public function __construct()
    {
        $this->loadClassNames();
    }

    public function loadClassNames()
    {

        $class = get_called_class();

        $parts = explode('\\', strtolower(get_called_class()));
        unset($parts[1]);
        $service = implode('.', $parts);

        $this->service = $service;
        $this->repository = str_replace('\Controller\\', '\Repository\\', $class);
        $this->entity = str_replace('\Controller\\', '\Entity\\', $class);
        $this->form = str_replace('\Controller\\', '\Form\\', $class);
        $this->controller = trim(strtolower(preg_replace('@([A-Z])@', "-$1", explode('\\', $class)[2])), '-');

    }

    protected function getRepository($entity = null)
    {
        if (null == $this->entityManager) {
            $this->entityManager = $this->getService('Doctrine\ORM\EntityManager');
        }
        if (null == $entity) {
            $entity = $this->entity;
        }
        return $this->entityManager->getRepository($entity);
    }

    protected function getService($service = false)
    {

        if (false === $service) {
            $service = $this->service;
        }

        return $this->getServiceLocator()->get($service);
    }

    protected function getForm()
    {
        try {
            return $this->getService($this->form);
        } catch (ServiceNotFoundException $e) {
            $di = new \Zend\Di\Di;
            return $di->get($this->form);
        }
    }

    public function getData()
    {
        $dontMap = array('entityManager', 'service', 'entity', 'eventIdentifier',
                         'plugins', 'request', 'response', 'event', 'events',
                         'serviceLocator', 'controller', 'messages', 'editView');

        $localVars = array_keys(get_object_vars($this));

        $data = array();
        foreach (array_diff($localVars, $dontMap) as $key) {
            $data[$key] = $this->$key;
        }

        return $data;
    }

    public function render($view = null, $data = null)
    {

        if (null === $data) {
            $data = array();
        }

        $result = new ViewModel($data + $this->getData());
        if (is_string($view)) {
            $result->setTemplate($view);
        }

        return $result;
    }

    public function getMessage($action, $type)
    {
        return $this->messages[$type][$action];
    }

    public function success($message)
    {
        $this->setFlash($message, 'success');
    }

    public function error($message)
    {
        $this->setFlash($message, 'error');
    }

    public function setFlash($message, $namespace = 'success')
    {
        $this->flashMessenger()->setNamespace($namespace)->addMessage($message);
    }

    public function getResource()
    {
        list ($namespace, $controller) = explode('\\', str_replace('\\controller\\', '\\', strtolower(get_called_class())));
        return "{$namespace}_{$controller}";
    }

}
