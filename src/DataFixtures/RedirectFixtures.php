<?php

namespace App\DataFixtures;

use App\Entity\Consultation;
use App\Entity\Redirect;
use App\Entity\Statement;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RedirectFixtures extends Fixture implements DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['dummy'];
    }

    public function getDependencies(): array
    {
        return [
            ConsultationStimmUndWahlrecht16JaehrigeFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(BasicFixtures::USER3);

        /** @var Consultation $consultation */
        $consultation = $this->getReference(ConsultationStimmUndWahlrecht16JaehrigeFixtures::CONSULTATION);

        /** @var Statement $statement */
        $statement = $this->getReference(ConsultationStimmUndWahlrecht16JaehrigeFixtures::STATEMENT);

        $redirect_consultation = new Redirect();
        $redirect_consultation->setToken('rc');
        $redirect_consultation->setCreatedBy($user);
        $redirect_consultation->setConsultation($consultation);
        $manager->persist($redirect_consultation);

        $redirect_statement = new Redirect();
        $redirect_statement->setToken('rs');
        $redirect_statement->setPassword(sha1('secret'));
        $redirect_statement->setCreatedBy($user);
        $redirect_statement->setStatement($statement);
        $manager->persist($redirect_statement);

        $manager->flush();
    }
}
