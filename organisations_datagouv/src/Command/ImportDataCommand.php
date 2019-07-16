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


class ImportDataCommand extends ContainerAwareCommand
{

    protected function configure()
    {
      // The name and description for the command in app/command
      $this ->setName('import:csv')
            ->setDescription('Import data from CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // Showing when the script is launched
      $now = new \DateTime();
      $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');

      // Importing CSV on DB via Doctrine ORM
      $organisation = new Organisation();
      $this->importFile($input, $output, 'public/data/organisations.csv', $organisation);
      $siren = new SirenDatagouv();
      $this->importFile($input, $output, 'public/data/siren-datagouv.csv', $siren);
      $twitter = new Twitter();
      $this->importFile($input, $output, 'public/data/twitter.csv', $twitter);


      // Showing when the script is over
      $now = new \DateTime();
      $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }

    protected function importFile(InputInterface $input, OutputInterface $output, $filename, $obj)
    {
      // Getting doctrine manager
      $em = $this->getContainer()->get('doctrine')->getManager();
      // Turning off doctrine default logs queries for saving memory (for big files)
      $em->getConnection()->getConfiguration()->setSQLLogger(null);
      // Getting php array of data from CSV
      $dataArray = $this->convert($filename);
      // Define the size of record, the frequency for persisting the data and the current index of records
      $size = count($dataArray);
      $batchSize = 1000;
      $i = 1;

      // Starting progress
      $progress = new ProgressBar($output, $size);
      $progress->start();

      // Processing on each row of data
      foreach($dataArray as $row) {
        $newEntry = $this->setData($row, $obj);
        // Persisting the current entry if it is new
        if($newEntry){
          $em->persist($newEntry);
        }
        // Each 1000 entries persisted we flush everything
        if (($i % $batchSize) === 0) {
          $em->flush();
          // Detaches all entries from Doctrine for memory save
          $em->clear();

        }
      $i++;
      }

      // Flushing and clear data on queue
      $em->flush();
      $em->clear();

      // Ending the progress bar process
      $now = new \DateTime();
      $progress->advance($size);
      $output->writeln($now->format('  d-m-Y G:i:s'));
      //$progress->finish();
    }

    public function setData($row, $obj)
    {
      if($obj instanceof Organisation){

        // Check if this object already exists in the database.
        // if not, it is added
        $organisation = $this->getContainer()->get('doctrine')->getManager()->getRepository(Organisation::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
        if(!is_object($organisation)){

          //Set fields for an Organisation entry
          $organisation = new Organisation();
          $organisation->setDatagouvid($row['datagouvid']);
          $organisation->setItem($row['item']);
          $organisation->setItemLabel($row['itemLabel']);
          return $organisation;
        }else{
          return false;
        }
      }elseif ($obj instanceof SirenDatagouv) {

        // Check if this object already exists in the database.
        // if not, it is added
        $siren = $this->getContainer()->get('doctrine')->getManager()->getRepository(SirenDatagouv::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
        if(!is_object($siren)){

          //Set fileds for a Siren entry
          $siren = new SirenDatagouv();
          $siren->setDatagouvid($row['datagouvid']);
          $siren->setSiren(intval($row['siren']));
          return $siren;

        }else{
          return false;
        }
      }elseif ($obj instanceof Twitter) {
        // Check if this object already exists in the database.
        // if not, it is added
        $twitter = $this->getContainer()->get('doctrine')->getManager()->getRepository(Twitter::class)->findOneBy(['datagouvid'=> $row['datagouvid']]);
        if(!is_object($twitter)){

          //Set fileds for a Twitter entry
          $twitter = new Twitter();
          $twitter->setDatagouvid($row['datagouvid']);
          $twitter->setTwitterUsername($row['Twitter_username']);
          return $twitter;

        }else{
          return false;
        }
      }
    }


  public function convert($filename, $delimiter = ',')
  {
    // Check if the file exist and is readable
    if(!file_exists($filename) || !is_readable($filename)) {
      return false;
    }

    // Initialize arrays
    $header = NULL;
    $data = array();

    // Parse the csv file
    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

        // Get the 1st line to set the names for each field
        if(!$header) {
          $header = $row;
        } else {
          //DEBUG print_r($header);
          //DEBUG print_r($row);
          $data[] = array_combine($header, $row);
        }
      }

      // Close the file
      fclose($handle);
    }
    return $data;
  }

}
