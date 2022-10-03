<?php

declare(strict_types=1);

namespace Lits\Action;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Interfaces\RouteCollectorProxyInterface as RouteCollectorProxy;

trait DatabaseFileTrait
{
    /** @return mixed[][] */
    abstract protected function file(): array;

    /**
     * @param array<string, string> $data
     * @throws HttpInternalServerErrorException
     */
    public function csv(
        ServerRequest $request,
        Response $response,
        array $data
    ): Response {
        return $this->writeFile(
            $request,
            $response,
            $data,
            'csv',
            'text/csv; charset=UTF-8'
        );
    }

    /**
     * @param array<string, string> $data
     * @throws HttpInternalServerErrorException
     */
    public function xlsx(
        ServerRequest $request,
        Response $response,
        array $data
    ): Response {
        return $this->writeFile(
            $request,
            $response,
            $data,
            'xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    public static function addFileRoutes(
        RouteCollectorProxy $router,
        string $path
    ): void {
        $path = \trim($path, '/');

        foreach (['csv', 'xlsx'] as $type) {
            $router->get('/' . $path . '/' . $type, [self::class, $type])
                ->setName($path . '/' . $type);
        }
    }

    /**
     * @param array<string, string> $data
     * @throws HttpInternalServerErrorException
     */
    private function writeFile(
        ServerRequest $request,
        Response $response,
        array $data,
        string $extension,
        string $contentType
    ): Response {
        $this->setup($request, $response, $data);

        $stream = \fopen('php://memory', 'r+');

        if ($stream === false) {
            throw new HttpInternalServerErrorException(
                $this->request,
                'Could not open memory stream'
            );
        }

        $spreadsheet = $this->writeFileSpreadsheet();

        $writer = IOFactory::createWriter($spreadsheet, \ucfirst($extension));
        $writer->save($stream);

        $name = \strtolower(
            \implode('_', \array_slice($this->hierarchy(), 1)) . '.' .
            $extension
        );

        try {
            return $this->response
                ->withFileDownload($stream, $name, $contentType)
                ->withHeader('Cache-Control', 'max-age=0');
        } catch (InvalidArgumentException $exception) {
            throw new HttpInternalServerErrorException(
                $this->request,
                'Could not download file',
                $exception
            );
        }
    }

    private function writeFileSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $col = 0;
        $row = 1;

        foreach ($this->file() as $file_row) {
            $col = 1;

            /** @var mixed $file_col */
            foreach ($file_row as $file_col) {
                $spreadsheet
                    ->getActiveSheet()
                    ->setCellValueExplicitByColumnAndRow(
                        $col,
                        $row,
                        $file_col,
                        DataType::TYPE_STRING
                    );

                $col++;
            }

            $row++;
        }

        while ($col > 0) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimensionByColumn($col)
                ->setAutoSize(true);

            $col--;
        }

        return $spreadsheet;
    }
}
