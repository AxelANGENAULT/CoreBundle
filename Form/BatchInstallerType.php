<?php

namespace Arii\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BatchInstallerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('title')
            ->add('description')
            ->add('licenceOptions')
            ->add('licence')
            ->add('os_target')
            ->add('installpath')
            ->add('userpath')
            ->add('Cron')
            ->add('cronCrontab')
            ->add('cronTimeout')
            ->add('cronSystab')
            ->add('cronChangeUser')
            ->add('cronChangeCommand')
            ->add('clusterOptions')
            ->add('schedulerId')
            ->add('schedulerHost')
            ->add('schedulerPort')
            ->add('schedulerAllowedHost')
            ->add('jobEvents')
            ->add('databaseDbms')
            ->add('databaseHost')
            ->add('databasePort')
            ->add('databaseSchema')
            ->add('databaseUser')
            ->add('databasePassword')
            ->add('connectorJTDS')
            ->add('connector')
            ->add('mailOnError')
            ->add('mailOnWarning')
            ->add('mailOnSuccess')
            ->add('mail')
            ->add('mailServer')
            ->add('mailPort')
            ->add('mailFrom')
            ->add('mailTo')
            ->add('mailCc')
            ->add('mailBcc')
            ->add('smtpAuth')
            ->add('smtpAccount')
            ->add('smtpPass')
            ->add('CheckUpdate')
            ->add('checkForUpdateStartDay')
            ->add('checkForUpdateStarttime')
            ->add('autoUpdateDownload')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arii\CoreBundle\Entity\BatchInstaller'
        ));
    }

    public function getName()
    {
        return 'arii_corebundle_batchinstallertype';
    }
}
