<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Hairdresser;
use App\Entity\OpeningHours;
use App\Entity\PortfolioPhoto;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\Speciality;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Données fictives de démonstration (W-07 : la v1 tourne uniquement sur des données Faker).
 *
 * Comptes créés (mot de passe commun : Test1234!)
 *  - admin@afrocoiffure.test                                     → ROLE_ADMIN
 *  - coiffeur1@afrocoiffure.test … coiffeur15@afrocoiffure.test  → ROLE_HAIRDRESSER
 *  - client1@afrocoiffure.test   … client20@afrocoiffure.test    → ROLE_CLIENT
 *
 * Chargement : php bin/console doctrine:fixtures:load
 */
class AppFixtures extends Fixture
{
    public const PASSWORD = 'Test1234!';
    private const NB_CLIENTS = 20;

    /**
     * Catégorie → spécialité → [nom de la prestation type, prix min, prix max, durée en minutes]
     */
    private const CATALOGUE = [
        'Tresses' => [
            'Box braids' => ['Box braids mi-longues', 90, 150, 300],
            'Knotless braids' => ['Knotless braids longues', 120, 200, 360],
            'Nattes collées (cornrows)' => ['Nattes collées simples', 30, 60, 90],
            'Fulani braids' => ['Fulani braids avec perles', 90, 140, 240],
            'Twists sénégalaises' => ['Twists sénégalaises', 100, 160, 300],
            'Passion twists' => ['Passion twists', 110, 170, 270],
        ],
        'Locks' => [
            'Création de locks' => ['Création de locks (départ)', 120, 250, 360],
            'Entretien de locks (retwist)' => ['Retwist complet', 50, 90, 120],
            'Faux locs' => ['Faux locs mi-longues', 120, 180, 360],
        ],
        'Tissages & perruques' => [
            'Tissage' => ['Tissage complet avec closure', 80, 150, 180],
            'Pose de perruque (lace wig)' => ['Pose de lace wig', 50, 100, 90],
            'Crochet braids' => ['Crochet braids', 60, 100, 150],
        ],
        'Coupes & soins' => [
            'Coupe homme / dégradé' => ['Coupe dégradée + contours', 15, 30, 45],
            'Soins cheveux naturels' => ['Soin hydratant cheveux crépus', 35, 60, 60],
            'Silk press' => ['Silk press (lissage sans produit chimique)', 50, 90, 120],
            'Défrisage' => ['Défrisage racines', 40, 70, 90],
        ],
        'Enfants' => [
            'Coiffure enfant' => ['Nattes ou tresses enfant', 25, 60, 90],
        ],
    ];

    /**
     * [nom, ville, code postal, latitude, longitude, type de lieu, statut, spécialités]
     * Coordonnées GPS = centre réel de la commune (un léger décalage est ajouté).
     */
    private const HAIRDRESSERS = [
        ['Queen Braids', 'Ixelles', '1050', 50.8336, 4.3626, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Box braids', 'Knotless braids', 'Fulani braids', 'Nattes collées (cornrows)']],
        ['Matongé Hair Studio', 'Ixelles', '1050', 50.8329, 4.3655, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Tissage', 'Pose de perruque (lace wig)', 'Silk press', 'Défrisage']],
        ['Locks & Love', 'Liège', '4000', 50.6326, 5.5797, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Création de locks', 'Entretien de locks (retwist)', 'Faux locs']],
        ['Nappy Roots', 'Namur', '5000', 50.4669, 4.8675, Hairdresser::WORKPLACE_HOME, Hairdresser::STATUS_APPROVED,
            ['Soins cheveux naturels', 'Twists sénégalaises', 'Nattes collées (cornrows)']],
        ['Fade Masters', 'Charleroi', '6000', 50.4108, 4.4446, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Coupe homme / dégradé', 'Nattes collées (cornrows)', 'Coiffure enfant']],
        ["Tresses d'Ébène", 'Mons', '7000', 50.4542, 3.9523, Hairdresser::WORKPLACE_MOBILE, Hairdresser::STATUS_APPROVED,
            ['Box braids', 'Knotless braids', 'Coiffure enfant', 'Crochet braids']],
        ['Crown & Curls', 'Antwerpen', '2000', 51.2194, 4.4025, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Silk press', 'Soins cheveux naturels', 'Pose de perruque (lace wig)']],
        ['Akwaba Coiffure', 'Gent', '9000', 51.0543, 3.7174, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Box braids', 'Tissage', 'Twists sénégalaises', 'Passion twists']],
        ['Black Pearl Beauty', 'Schaerbeek', '1030', 50.8676, 4.3737, Hairdresser::WORKPLACE_HOME, Hairdresser::STATUS_APPROVED,
            ['Knotless braids', 'Passion twists', 'Faux locs']],
        ['Kinky Twist Studio', 'Anderlecht', '1070', 50.8366, 4.3086, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Twists sénégalaises', 'Passion twists', 'Crochet braids', 'Fulani braids']],
        ['Mama Africa Hair', 'Bruxelles', '1000', 50.8467, 4.3525, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_APPROVED,
            ['Tissage', 'Défrisage', 'Coiffure enfant', 'Nattes collées (cornrows)']],
        ['Louvain Locks', 'Ottignies-Louvain-la-Neuve', '1348', 50.6681, 4.6118, Hairdresser::WORKPLACE_MOBILE, Hairdresser::STATUS_APPROVED,
            ['Création de locks', 'Entretien de locks (retwist)']],
        // Fiches en attente de validation (F-27) et refusée : pour tester le back-office
        ['Bantu Style', 'Leuven', '3000', 50.8798, 4.7005, Hairdresser::WORKPLACE_HOME, Hairdresser::STATUS_PENDING,
            ['Nattes collées (cornrows)', 'Twists sénégalaises', 'Soins cheveux naturels']],
        ['Ébène Coiffure', 'Tournai', '7500', 50.6056, 3.3875, Hairdresser::WORKPLACE_SALON, Hairdresser::STATUS_PENDING,
            ['Box braids', 'Tissage', 'Coupe homme / dégradé']],
        ['Afro Glam', 'Verviers', '4800', 50.5891, 5.8622, Hairdresser::WORKPLACE_MOBILE, Hairdresser::STATUS_REJECTED,
            ['Pose de perruque (lace wig)', 'Crochet braids']],
    ];

    private const POSITIVE_COMMENTS = [
        "Travail impeccable, mes box braids ont tenu plus de six semaines. Je recommande !",
        "Très professionnelle et à l'écoute. Elle a pris le temps de bien comprendre ce que je voulais.",
        "Super ambiance, ponctuelle et le résultat est magnifique. Je reviendrai sans hésiter.",
        "Enfin quelqu'un qui sait vraiment s'occuper des cheveux crépus ! Mes cheveux sont en pleine forme.",
        "Rapide, propre et soigné. Le prix est tout à fait correct pour la qualité.",
        "Ma fille était ravie de ses tresses, très douce avec les enfants.",
        "Les contours sont parfaits, coupe nette. Mon coiffeur attitré désormais.",
        "Accueil chaleureux, conseils utiles pour l'entretien. Très satisfaite du résultat.",
        "Mes locks n'ont jamais été aussi propres et bien entretenues. Merci !",
        "Pose de perruque bluffante, on ne voit pas du tout la lace. Bravo.",
    ];

    private const MIXED_COMMENTS = [
        "Joli résultat, mais un peu plus long que prévu. Prévoyez votre après-midi.",
        "Bon travail dans l'ensemble, quelques tresses un peu serrées sur l'avant.",
        "Résultat correct, mais le rendez-vous a commencé avec 45 minutes de retard.",
        "Sympathique et compétente, même si le prix final était plus élevé qu'annoncé.",
    ];

    private const NEGATIVE_COMMENTS = [
        "Déçue : les tresses ont commencé à se défaire au bout d'une semaine.",
        "Rendez-vous annulé au dernier moment, sans vraie explication.",
        "Trop serré, j'ai eu mal pendant plusieurs jours. Je ne reviendrai pas.",
    ];

    private const CONTACT_MESSAGES = [
        "Bonjour, je souhaiterais prendre rendez-vous pour des box braids mi-longues. Avez-vous des disponibilités ?",
        "Bonjour, est-ce possible de venir avec ma fille de 7 ans pour des nattes collées ?",
        "Bonjour, je voudrais un devis pour des knotless braids longues avec mèches fournies.",
        "Hello, je cherche quelqu'un pour un retwist de mes locks. Êtes-vous disponible un samedi ?",
        "Bonjour, vous déplacez-vous à domicile ? J'habite à une dizaine de kilomètres.",
        "Bonjour, combien de temps faut-il prévoir pour un tissage complet avec closure ?",
    ];

    private Generator $faker;
    private Filesystem $filesystem;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->faker = Factory::create('fr_BE');
        $this->faker->seed(2026); // mêmes données à chaque chargement
        $this->filesystem = new Filesystem();
    }

    public function load(ObjectManager $manager): void
    {
        // Un seul hachage, réutilisé pour tous les comptes (beaucoup plus rapide)
        $passwordHash = $this->hasher->hashPassword(new User(), self::PASSWORD);

        $specialities = $this->loadCatalogue($manager);
        $this->loadAdmin($manager, $passwordHash);
        $clients = $this->loadClients($manager, $passwordHash);
        $hairdressers = $this->loadHairdressers($manager, $passwordHash, $specialities);

        $approved = array_values(array_filter(
            $hairdressers,
            static fn (Hairdresser $h) => $h->isApproved()
        ));

        $this->loadReviews($manager, $approved, $clients);
        $this->loadFavorites($clients, $approved);
        $this->loadContactRequests($manager, $approved, $clients);

        $manager->flush();
    }

    /**
     * @return array<string, Speciality> spécialités indexées par nom
     */
    private function loadCatalogue(ObjectManager $manager): array
    {
        $specialities = [];

        foreach (self::CATALOGUE as $categoryName => $items) {
            $category = (new Category())->setName($categoryName);
            $manager->persist($category);

            foreach (array_keys($items) as $specialityName) {
                $speciality = (new Speciality())->setName($specialityName);
                $category->addSpeciality($speciality);
                $manager->persist($speciality);
                $specialities[$specialityName] = $speciality;
            }
        }

        return $specialities;
    }

    private function loadAdmin(ObjectManager $manager, string $passwordHash): void
    {
        $admin = (new User())
            ->setEmail('admin@afrocoiffure.test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($passwordHash)
            ->setFirstname('Admin')
            ->setLastname('Afrocoiffure')
            ->setConsentAt(new \DateTimeImmutable('-1 year'))
            ->setCreatedAt(new \DateTimeImmutable('-1 year'));

        $manager->persist($admin);
    }

    /**
     * @return User[]
     */
    private function loadClients(ObjectManager $manager, string $passwordHash): array
    {
        $clients = [];

        for ($i = 1; $i <= self::NB_CLIENTS; ++$i) {
            $createdAt = $this->randomDate('-11 months', '-1 week');

            $client = (new User())
                ->setEmail(sprintf('client%d@afrocoiffure.test', $i))
                ->setRoles(['ROLE_CLIENT'])
                ->setPassword($passwordHash)
                ->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setPhone($this->faker->boolean(60) ? $this->faker->phoneNumber() : null)
                ->setConsentAt($createdAt)
                ->setCreatedAt($createdAt);

            $manager->persist($client);
            $clients[] = $client;
        }

        return $clients;
    }

    /**
     * @param array<string, Speciality> $specialities
     *
     * @return Hairdresser[]
     */
    private function loadHairdressers(ObjectManager $manager, string $passwordHash, array $specialities): array
    {
        $this->resetDemoPhotos();
        $hairdressers = [];

        foreach (self::HAIRDRESSERS as $index => [$name, $city, $postalCode, $lat, $lng, $workplace, $status, $specialityNames]) {
            $number = $index + 1;
            $createdAt = $status === Hairdresser::STATUS_APPROVED
                ? $this->randomDate('-1 year', '-2 months')
                : $this->randomDate('-3 weeks', '-1 day');

            // Compte du coiffeur (RM-01 : un compte = une fiche)
            $user = (new User())
                ->setEmail(sprintf('coiffeur%d@afrocoiffure.test', $number))
                ->setRoles(['ROLE_HAIRDRESSER'])
                ->setPassword($passwordHash)
                ->setFirstname($this->faker->firstNameFemale())
                ->setLastname($this->faker->lastName())
                ->setPhone($this->faker->phoneNumber())
                ->setConsentAt($createdAt)
                ->setCreatedAt($createdAt);
            $manager->persist($user);

            $hairdresser = (new Hairdresser())
                ->setName($name)
                ->setCity($city)
                ->setPostalCode($postalCode)
                ->setAddress($this->faker->streetAddress())
                ->setLatitude(round($lat + $this->faker->randomFloat(4, -0.012, 0.012), 6))
                ->setLongitude(round($lng + $this->faker->randomFloat(4, -0.012, 0.012), 6))
                ->setPhone($user->getPhone())
                ->setWorkplaceType($workplace)
                ->setStatus($status)
                ->setDescription($this->buildDescription($name, $city, $workplace, $specialityNames))
                ->setCreatedAt($createdAt);
            $user->setHairdresser($hairdresser);
            $manager->persist($hairdresser);

            $this->loadOpeningHours($manager, $hairdresser, $workplace);

            foreach ($specialityNames as $specialityName) {
                $speciality = $specialities[$specialityName];
                $this->loadService($manager, $hairdresser, $speciality);
                $this->loadPhotos($manager, $hairdresser, $speciality, $number);
            }

            $hairdressers[] = $hairdresser;
        }

        return $hairdressers;
    }

    private function loadOpeningHours(ObjectManager $manager, Hairdresser $hairdresser, string $workplace): void
    {
        $open = $this->faker->randomElement(['09:00', '09:30', '10:00']);
        $close = $this->faker->randomElement(['18:00', '18:30', '19:00']);
        $closedOnMonday = $workplace === Hairdresser::WORKPLACE_SALON || $this->faker->boolean();

        foreach (array_keys(OpeningHours::DAYS) as $day) {
            $hours = (new OpeningHours())->setDayOfWeek($day);

            if ($day === 7 || ($day === 1 && $closedOnMonday)) {
                $hours->setIsClosed(true);
            } elseif ($day === 6) {
                // Le samedi : journée plus courte
                $hours->setOpenTime(new \DateTimeImmutable('09:00'))
                    ->setCloseTime(new \DateTimeImmutable('17:00'));
            } else {
                $hours->setOpenTime(new \DateTimeImmutable($open))
                    ->setCloseTime(new \DateTimeImmutable($close));
            }

            $hairdresser->addOpeningHour($hours);
            $manager->persist($hours);
        }
    }

    private function loadService(ObjectManager $manager, Hairdresser $hairdresser, Speciality $speciality): void
    {
        [$serviceName, $minPrice, $maxPrice, $duration] = $this->findCatalogueItem($speciality->getName());

        // Prix arrondi à 5 € près, entre le min et le max de la prestation type
        $price = (int) (round($this->faker->numberBetween($minPrice, $maxPrice) / 5) * 5);

        $service = (new Service())
            ->setName($serviceName)
            ->setPrice(number_format($price, 2, '.', ''))
            ->setDurationMinutes($duration)
            ->setDescription($this->faker->boolean(60)
                ? 'Mèches ' . $this->faker->randomElement(['fournies', 'non fournies', 'fournies sur demande']) . '. Shampoing compris.'
                : null)
            ->setSpeciality($speciality);
        $hairdresser->addService($service);
        $manager->persist($service);
    }

    private function loadPhotos(ObjectManager $manager, Hairdresser $hairdresser, Speciality $speciality, int $number): void
    {
        $colors = ['#7B3F2A', '#A0522D', '#4E342E', '#8D6E63', '#C27C4E', '#5D4037', '#B5651D'];

        $nbPhotos = $this->faker->numberBetween(1, 2);

        for ($i = 1; $i <= $nbPhotos; ++$i) {
            $filename = sprintf('demo-%d-%s-%d.svg', $number, $speciality->getSlug(), $i);
            $this->createPlaceholderImage($filename, (string) $speciality->getName(), $this->faker->randomElement($colors));

            $photo = (new PortfolioPhoto())
                ->setFilename($filename)
                ->setCaption($speciality->getName() . ' – réalisation')
                ->setSpeciality($speciality)
                ->setCreatedAt($this->randomDate('-6 months', 'now'));
            $hairdresser->addPortfolioPhoto($photo);
            $manager->persist($photo);
        }
    }

    /**
     * @param Hairdresser[] $hairdressers seulement les fiches validées
     * @param User[]        $clients
     */
    private function loadReviews(ObjectManager $manager, array $hairdressers, array $clients): void
    {
        foreach ($hairdressers as $hairdresser) {
            // Des auteurs différents pour chaque fiche (un seul avis par client et par coiffeur)
            $authors = $this->faker->randomElements($clients, $this->faker->numberBetween(2, 8));

            foreach ($authors as $author) {
                $rating = $this->faker->randomElement([5, 5, 5, 5, 4, 4, 4, 3, 3, 2, 1]);
                $comment = match (true) {
                    $rating >= 4 => $this->faker->randomElement(self::POSITIVE_COMMENTS),
                    $rating === 3 => $this->faker->randomElement(self::MIXED_COMMENTS),
                    default => $this->faker->randomElement(self::NEGATIVE_COMMENTS),
                };

                // Modération a posteriori : quelques avis masqués par l'admin, d'autres signalés en attente
                $hidden = $this->faker->boolean(8);

                $review = (new Review())
                    ->setRating($rating)
                    ->setComment($comment)
                    ->setStatus($hidden ? Review::STATUS_HIDDEN : Review::STATUS_PUBLISHED)
                    ->setIsReported($hidden || $this->faker->boolean(5))
                    ->setAuthor($author)
                    ->setCreatedAt($this->randomDate('-10 months', '-1 day'));
                $hairdresser->addReview($review);
                $manager->persist($review);
            }
        }
    }

    /**
     * @param User[]        $clients
     * @param Hairdresser[] $hairdressers
     */
    private function loadFavorites(array $clients, array $hairdressers): void
    {
        foreach ($clients as $client) {
            foreach ($this->faker->randomElements($hairdressers, $this->faker->numberBetween(0, 4)) as $favorite) {
                $client->addFavorite($favorite);
            }
        }
    }

    /**
     * @param Hairdresser[] $hairdressers
     * @param User[]        $clients
     */
    private function loadContactRequests(ObjectManager $manager, array $hairdressers, array $clients): void
    {
        foreach ($hairdressers as $hairdresser) {
            $nbRequests = $this->faker->numberBetween(1, 5);

            for ($i = 0; $i < $nbRequests; ++$i) {
                $status = $this->faker->randomElement([
                    ContactRequest::STATUS_NEW, ContactRequest::STATUS_NEW,
                    ContactRequest::STATUS_ACCEPTED, ContactRequest::STATUS_ACCEPTED,
                    ContactRequest::STATUS_REFUSED,
                ]);

                if ($status === ContactRequest::STATUS_NEW) {
                    // Demande récente, date souhaitée dans le futur
                    $createdAt = $this->randomDate('-10 days', 'now');
                    $desiredDate = (new \DateTimeImmutable('today'))->modify(sprintf('+%d days', $this->faker->numberBetween(3, 40)));
                } else {
                    // Demande déjà traitée par le coiffeur
                    $createdAt = $this->randomDate('-4 months', '-3 weeks');
                    $desiredDate = $createdAt->setTime(0, 0)->modify(sprintf('+%d days', $this->faker->numberBetween(3, 15)));
                }

                $request = (new ContactRequest())
                    ->setMessage($this->faker->randomElement(self::CONTACT_MESSAGES))
                    ->setStatus($status)
                    ->setCreatedAt($createdAt)
                    ->setDesiredDate($this->faker->boolean(80) ? $desiredDate : null);

                // Prestation souhaitée : facultative
                $services = $hairdresser->getServices()->toArray();
                if ($services !== [] && $this->faker->boolean(70)) {
                    $request->setService($this->faker->randomElement($services));
                }

                if ($this->faker->boolean(60)) {
                    // Client connecté
                    $sender = $this->faker->randomElement($clients);
                    $request->setName($sender->getFullName())
                        ->setEmail((string) $sender->getEmail())
                        ->setPhone($sender->getPhone());
                    $sender->addContactRequest($request);
                } else {
                    // Visiteur sans compte (F-14)
                    $request->setName($this->faker->name())
                        ->setEmail($this->faker->safeEmail())
                        ->setPhone($this->faker->boolean(50) ? $this->faker->phoneNumber() : null);
                }

                $hairdresser->addContactRequest($request);
                $manager->persist($request);
            }
        }
    }

    // ---------------------------------------------------------------- Outils

    /**
     * @return array{0: string, 1: int, 2: int, 3: int}
     */
    private function findCatalogueItem(?string $specialityName): array
    {
        foreach (self::CATALOGUE as $items) {
            if (isset($items[$specialityName])) {
                return $items[$specialityName];
            }
        }

        throw new \LogicException(sprintf('Spécialité inconnue dans le catalogue : %s', $specialityName));
    }

    /**
     * @param string[] $specialityNames
     */
    private function buildDescription(string $name, string $city, string $workplace, array $specialityNames): string
    {
        $where = match ($workplace) {
            Hairdresser::WORKPLACE_HOME => sprintf('vous reçoit à domicile à %s', $city),
            Hairdresser::WORKPLACE_MOBILE => sprintf('se déplace chez vous à %s et dans les environs', $city),
            default => sprintf('vous accueille dans son salon à %s', $city),
        };

        return sprintf(
            "%s %s. Spécialités : %s. Cheveux crépus, frisés et bouclés : chaque coiffure est réalisée avec soin, dans le respect de vos cheveux naturels.",
            $name,
            $where,
            mb_strtolower(implode(', ', $specialityNames))
        );
    }

    private function randomDate(string $from, string $to): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween($from, $to));
    }

    private function uploadDir(): string
    {
        return $this->projectDir . '/public/' . PortfolioPhoto::UPLOAD_DIR;
    }

    /**
     * Supprime les anciennes images de démo avant de les régénérer (les vrais uploads ne sont pas touchés).
     */
    private function resetDemoPhotos(): void
    {
        if (!is_dir($this->uploadDir())) {
            return;
        }

        $this->filesystem->remove(glob($this->uploadDir() . '/demo-*.svg') ?: []);
    }

    /**
     * Crée une image SVG de remplacement : aucune vraie photo n'est nécessaire pour la démo.
     */
    private function createPlaceholderImage(string $filename, string $label, string $color): void
    {
        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">'
            . '<rect width="800" height="800" fill="%s"/>'
            . '<text x="400" y="390" font-family="Arial, sans-serif" font-size="44" fill="#FFFFFF" text-anchor="middle">%s</text>'
            . '<text x="400" y="450" font-family="Arial, sans-serif" font-size="26" fill="#FFFFFF" fill-opacity="0.8" text-anchor="middle">Photo de démonstration</text>'
            . '</svg>',
            $color,
            htmlspecialchars($label, ENT_XML1)
        );

        $this->filesystem->dumpFile($this->uploadDir() . '/' . $filename, $svg);
    }
}
