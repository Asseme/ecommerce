<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
<<<<<<< HEAD
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
=======
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
>>>>>>> 9d43ba7936ff0301822e5983d2f6e87f1608950a

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('stock')
            ->add('photo', FileType::class, [
<<<<<<< HEAD
                'mapped' => false,
                'required' => false,
=======
                'label' => 'Photo',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
>>>>>>> 9d43ba7936ff0301822e5983d2f6e87f1608950a
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
<<<<<<< HEAD
                            'image/jpg',
                            'image/jpeg',
=======
                            'image/jpeg',
                            'image/jpg',
>>>>>>> 9d43ba7936ff0301822e5983d2f6e87f1608950a
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
<<<<<<< HEAD
            ->add('contenuPaniers')
=======
            /* ->add('contenuPaniers') */
>>>>>>> 9d43ba7936ff0301822e5983d2f6e87f1608950a
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
