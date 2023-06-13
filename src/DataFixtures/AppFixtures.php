<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Contact;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;


    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        //Users
        $users = [];

        $admin = new User();
        $admin->setFullName('Administrateur de Blog V2')
            ->setPseudo(null)
            ->setEmail('admin@blogv2.fr')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');

        $users[] = $admin;
        $manager->persist($admin);

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
            $users[] = $user;
            $manager->persist($user);
        }

        //Articles
        $articles = [];

        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setTitle(implode(' ', $this->faker->words(5)))
                ->setSlug('title')
                ->setContent($this->faker->paragraph(60))
                ->setFeaturedText($this->faker->sentence());
            $articles[] = $article;
            $manager->persist($article);
        }


        //Ingrédients
        $ingredients = [];
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 1000))
                ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }


        //Recettes
        for ($j = 0; $j < 50; $j++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 180))
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(400))
                ->setPrice(mt_rand(0, 5000))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }

            $manager->persist($recipe);
        }


        //Contact
        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . ($i + 1))
                ->setMessage($this->faker->text());

            $manager->persist($contact);
        }

        $manager->flush();
    }
}
