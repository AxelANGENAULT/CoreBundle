<?php

namespace Arii\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SpoolerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scheduler')
            ->add('cluster_options')
            ->add('licence')
            ->add('install_path')
            ->add('user_path')
            ->add('events')
            ->add('os_target')
            ->add('version')
            ->add('install_date')
            ->add('status')
            ->add('status_date')
            ->add('connection')
            ->add('transfer')
            ->add('db')
            ->add('smtp')
            ->add('http')
            ->add('supervisor')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arii\CoreBundle\Entity\Spooler'
        ));
    }

    public function getName()
    {
        return 'arii_corebundle_spoolertype';
    }
}
