<?php
namespace Paustian\WebsiteFeeModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/**
 * Description of ExamForm
 * Set up the elements for a Exam form.
 *
 * @author paustian
 * 
 */
class SubscriberForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wsfsubsname', 'text', array('label' => __('Subscription Name'), 'required' => true))
            ->add('wsfitem', 'integer', array('label' => __('Subscription Item Number'), 'required' => true))
            ->add('wsfpaymentamount', 'integer', array('label' => __('Payment Amount'), 'required' => true))
            ->add('wsfemail', 'email', array('label' => __('Provider Email'), 'required' => true)) 
            ->add('wsfgroupid', 'integer', array('label' => __('The group to subscribe'), 'required' => true))
            ->add('save', 'submit', array('label' => 'Save Subscription'))
            ->add('cancel', 'button', array('label' => __('Cancel')));
        
    }

    public function getName()
    {
        return 'paustianwebsitefeemodule_subscriberform';
    }

    /**
     * OptionsResolverInterface is @deprecated and is supposed to be replaced by
     * OptionsResolver but docs not clear on implementation
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity',
        ));
    }
}

