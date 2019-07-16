<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Validator\Constraints\DateTime;

use App\Entity\Organisation;
use App\Entity\SirenDatagouv;
use App\Entity\Twitter;


class EmptyDataBaseCommand extends ContainerAwareCommand
{

  protected function configure()
  {
    // The name and description for the command in app/command
    $this ->setName('database:empty')
          ->setDescription('Empty the databse');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Getting doctrine manager
    $em = $this->getContainer()->get('doctrine')->getManager();

    $now = new \DateTime();

    // Get all Organisation entities
    $allOrganisations = $em->getRepository(Organisation::class)->findAll();

    // Starting progress bar
    $progress = new ProgressBar($output, count($allOrganisations));
    $progress->start();

    // Delete data
    foreach($allOrganisations as $organisation){
      $em->remove($organisation);
    }
    $em->flush();

    // End progress bar
    $progress->advance(count($allOrganisations));
    $output->writeln($now->format('  d-m-Y G:i:s'));

    // Get all SirenDatagouv entities
    $allSiren = $em->getRepository(SirenDatagouv::class)->findAll();

    // Starting progress bar
    $progress = new ProgressBar($output, count($allSiren));
    $progress->start();

    // Delete data
    foreach($allSiren as $siren){
      $em->remove($siren);
    }
    $em->flush();

    // End progress
    $progress->advance(count($allSiren));
    $output->writeln($now->format('  d-m-Y G:i:s'));

    // Get all Twitter entities
    $allTwitters = $em->getRepository(Twitter::class)->findAll();

    // Starting progress bar
    $progress = new ProgressBar($output, count($allTwitters));
    $progress->start();

    // Delete Data
    foreach($allTwitters as $twitter){
      $em->remove($twitter);
    }
    $em->flush();

    // End progress
    $progress->advance(count($allTwitters));
    $output->writeln($now->format('  d-m-Y G:i:s'));
  }


}
