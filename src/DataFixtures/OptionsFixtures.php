<?php

namespace App\DataFixtures;

use App\Entity\Option;
use Doctrine\DBAL\Types\TextType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OptionsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $options[] = new Option('Titre du blog', 'blog_about', 'Blog V2', TextType::class);
        $options[] = new Option('Texte du Copyright', 'blog_copyright', 'Tous droits réservés', TextType::class);
        $options[] = new Option('Nombre d\'articles par page', 'blog_articles_limit', 5, NumberType::class);
        $options[] = new Option('Tout le monde peut s\'inscrire', 'blog_can_register', true, CheckboxType::class);

        foreach ($options as $option) {
            $manager->persist($option);
        }


        $manager->flush();
    }
}
