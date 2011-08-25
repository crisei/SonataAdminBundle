<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Filter;

use Sonata\AdminBundle\Filter\Filter as BaseFilter;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Form\FormBuilder;
use Sonata\AdminBundle\Filter\FilterFactoryInterface;
use Sonata\AdminBundle\Guesser\TypeGuesserInterface;
use Symfony\Component\Form\FormFactory;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterFactory implements FilterFactoryInterface
{
    protected $container;

    protected $types;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $types
     */
    public function __construct(ContainerInterface $container, array $types = array())
    {
        $this->container = $container;
        $this->types     = $types;
    }

    /**
     * @throws \RunTimeException
     * @param $name
     * @param $type
     * @param $options
     * @return object|\Symfony\Component\DependencyInjection\The
     */
    public function create($name, $type, $options)
    {
        if (!$type) {
            throw new \RunTimeException('The type must be defined');
        }

        $id = isset($this->types[$type]) ? $this->types[$type] : false;

        if (!$id) {
            throw new \RunTimeException(sprintf('No attached service to type named `%s`', $type));
        }

        $filter = $this->container->get($id);

        if (!$filter instanceof FilterInterface) {
            throw new \RunTimeException(sprintf('The service `%s` must implement `FilterInterface`', $id));
        }

        $filter->initialize($name, $options);

        return $filter;
    }
}