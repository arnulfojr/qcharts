<?php

namespace QCharts\CoreBundle\Command;

use Cron\CronExpression;
use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\AbortedOperationException;
use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\Service\Snapshot\SnapshotService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;

class QueryUpdateCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $description = "Will debug the errors and wait for user response, if enabled";
        $this
            ->setName("qcharts:update:snapshots")
            ->setDescription("checks if the corresponding snapshots need to be updated")
            ->addOption("debug", 'd' , InputOption::VALUE_NONE, $description);
    }

    /**
     * @return array
     */
    static public function getModes()
    {
        return ['Live', 'Snapshot', 'Time Machine'];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("[ QCharts updating ]");
        //update the ones that are due
        $date = new DateTime();
        $_code = 0;
        try
        {
            $debugMode = $input->getOption("debug");
            $queryService = $this->getContainer()->get("qcharts.query");
            $queries = $queryService->getPreFetchedQueries();

            $dateFormat = SnapshotService::FILE_DATE_FORMAT;

            $io->note("QCharts date format: '{$dateFormat}', date now: {$date->format($dateFormat)}");

            $queriesQty = count($queries);

            $paths = $this->getContainer()->getParameter("qcharts.paths");

            $io->note("Saving in directory: {$paths['snapshots']}");

            $io->progressStart($queriesQty);
            $io->newLine(2);

            foreach ($queries as $query)
            {
                /** @var QueryRequest $query */
                try
                {
                    $this->updateQuery($query, $io, $date);
                }
                catch (\Exception $e)
                {
                    $io->error("Error updating '{$query->getTitle()}', {$e->getMessage()}, moving along...");
                    if ($debugMode && !$io->confirm("Want to continue?", true))
                    {
                        throw new AbortedOperationException("The operation was aborted by the user", 1);
                    }
                }
            }
        }
        catch (AbortedOperationException $e)
        {
            $_code = $e->getCode();
            $io->error($e->getMessage());
        }
        catch (\Exception $e)
        {
            $_code = 1;
            $io->error("{$e->getMessage()}");
        }
        finally
        {
            ($_code) ? $io->progressFinish() : null;
            $io->newLine(3);
            $io->text("[ QCharts finished with code: {$_code} ]");
        }

        $io->title("[ QCharts end updating ]");
    }

    /**
     * @param QueryRequest $query
     * @param SymfonyStyle $io
     * @param DateTime $date
     * @throws \QCharts\CoreBundle\Exception\WriteReadException
     */
    protected function updateQuery(QueryRequest $query, SymfonyStyle $io, DateTime $date)
    {
        $modes = QueryUpdateCommand::getModes();
        $dateFormat = SnapshotService::FILE_DATE_FORMAT;

        /** @var SnapshotService $snapshotService */
        $snapshotService = $this->getContainer()->get("qcharts.core.snapshot_service");
        /** @var QueryRepository $qrRepo */
        $qrRepo = $this->getContainer()->get("qcharts.query_repo");

        $cron = CronExpression::factory($query->getCronExpression());

        $queryDate = $query->getConfig()->getFetchedOn()->format($dateFormat);

        $io->newLine();
        $io->section("QCharts checking: '{$query->getTitle()}' with date: {$queryDate}");

        if ($cron->isDue($date))
        {
            //update it!
            $duration = $snapshotService->updateSnapshot($query);

            $io->newLine(1);
            $io->note("QCharts updating:");
            $io->table([
                'Title', 'CronExpression', 'Last fetch', 'Mode', 'Query execution time'
            ], [
                [
                    $query->getTitle(),
                    $query->getCronExpression(),
                    $query->getConfig()->getFetchedOn()->format($dateFormat),
                    $modes[$query->getConfig()->getIsCached()],
                    "{$duration} secs."
                ]
            ]);

            $qrRepo->setUpdatedOn($query, $date);
        }

        $io->success("QCharts '{$query->getTitle()}' is up to date, next run: {$cron->getNextRunDate()->format($dateFormat)}");
        $io->progressAdvance( 1 );
        $io->newLine(2);
    }

}