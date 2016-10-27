<?php

namespace Iphp\FileStoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Iphp\FileStoreBundle\FileStorage\FileStorageInterface;
use Iphp\FileStoreBundle\DataStorage\DataStorageInterface;
use Iphp\FileStoreBundle\Mapping\PropertyMappingFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\CreationException;
use Symfony\Component\Form\FormView;


use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use Iphp\FileStoreBundle\Form\DataTransformer\FileDataTransformer;
use Iphp\FileStoreBundle\Form\DataTransformer\FileDataViewTransformer;

/**
 * @author Vitiko <vitiko@mail.ru>
 */
class FileType extends AbstractType
{

    /**
     * @var \Iphp\FileStoreBundle\Mapping\PropertyMappingFactory
     */
    protected $mappingFactory;

    /**
     * @var \Iphp\FileStoreBundle\DataStorage\DataStorageInterface
     */
    protected  $dataStorage;

    /**
     * @var \Iphp\FileStoreBundle\FileStorage\FileStorageInterface
     */
    protected $fileStorage;

    public function __construct(PropertyMappingFactory $mappingFactory,
                                DataStorageInterface $dataStorage,
                                FileStorageInterface $fileStorage)
    {
        $this->mappingFactory = $mappingFactory;
        $this->dataStorage = $dataStorage;
        $this->fileStorage = $fileStorage;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'read_only' => false,
            'upload' => true,
            'show_uploaded' => true

        ));
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $transformer = new FileDataTransformer($this->fileStorage);
        $subscriber = new FileTypeBindSubscriber($this->mappingFactory,$this->dataStorage,  $transformer);
        $builder->addEventSubscriber($subscriber);


        $builder->add('file', \Symfony\Component\Form\Extension\Core\Type\FileType::class, array('required' => false))
            ->add('delete', CheckboxType::class, array('required' => false))
            ->addViewTransformer($transformer);

        //for sonata admin
        //    ->addViewTransformer(new FileDataViewTransformer());
    }

    public function getBlockPrefix()
    {
        return 'iphp_file';
    }
}



