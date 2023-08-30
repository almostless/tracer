<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Tracer\Adapter\Reporter;

use Hyperf\Contract\StdoutLoggerInterface;
use RuntimeException;
use Zipkin\Reporter;

use function Hyperf\Support\make;

class ReporterFactory
{
    public function __construct(
        private HttpClientFactory $httpClientFactory,
        private StdoutLoggerInterface $logger
    ) {
    }

    public function make(array $option = []): Reporter
    {
        $class = $option['class'] ?? '';

        if ($class === \Zipkin\Reporters\Http::class) {
            $option['constructor']['requesterFactory'] = $this->httpClientFactory;
            $option['constructor']['logger'] = $this->logger;
        }
        $constructor = $option['constructor'] ?? [];
        if (! class_exists($class)) {
            throw new RuntimeException(sprintf('Class %s is not exists.', $class));
        }

        if (! is_a($class, Reporter::class, true)) {
            throw new RuntimeException('Unsupported reporter.');
        }

        return make($class, $constructor);
    }
}