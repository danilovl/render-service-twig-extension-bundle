<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Danilovl\RenderServiceTwigExtensionBundle\Service\{
    TwigExtensionItem,
    TwigExtensionContainer
};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'danilovl:render-service:list', description: 'List of extensions.')]
class ListCommand extends Command
{
    public function __construct(private readonly TwigExtensionContainer $extensionContainer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = (new Table($output))
            ->setHeaders(['Type', 'Name', 'Service', 'Method', 'Options']);

        $functions = $this->extensionContainer->getFunctions();
        $this->addRows('Function', $table, $functions);

        $filters = $this->extensionContainer->getFilters();
        $this->addRows('Filter', $table, $filters);

        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @param TwigExtensionItem[] $data
     */
    private function addRows(string $type, Table $table, array $data): void
    {
        foreach ($data as $function) {
            $table->addRow([
                $type,
                $function->name,
                $function->service,
                $function->method,
                $this->getOptionRow($function->options)
            ]);
        }
    }

    private function getOptionRow(array $options): string
    {
        $result = '';
        foreach ($options as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_array($value)) {
                $value = json_encode($value);
            }

            $result .= $key . ': ' . $value . "\n";
        }

        return $result;
    }
}
