<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Xepozz\TestIt\MatrixIntersection;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\ValueGenerator\ValueGeneratorInterface;
use Xepozz\TestIt\ValueGenerator\ValueGeneratorRepository;

final class TestCaseGenerator
{
    public function __construct(
        private readonly TypeNormalizer $typeNormalizer,
        private readonly ValueGeneratorRepository $valueGeneratorRepository,
        private readonly MatrixIntersection $intersection,
    ) {
    }

    /**
     * @return array[]
     */
    public function generate(Context $context): iterable
    {
        $method = $context->method;

        $denormalizedType = $this->typeNormalizer->denormalize($method->getReturnType());
        foreach ($denormalizedType as $type) {
            $valueGenerator = $this->valueGeneratorRepository->getByType($type);

            $valueGenerators = [];

            if ($valueGenerator !== null && !$context->config->isCaseEvaluationEnabled()) {
                $valueGenerators[] = $valueGenerator;
            }

            foreach ($method->getParams() as $parameter) {
                $denormalizedType = $this->typeNormalizer->denormalize($parameter->type);
                $parameterValueGenerator = $this->valueGeneratorRepository->getByType($denormalizedType[0]);

                if ($parameterValueGenerator === null) {
                    continue;
                }
                $valueGenerators[] = $parameterValueGenerator;
            }

            yield from $this->intersection->intersect(
                ...array_map(fn (ValueGeneratorInterface $generator) => $generator->generate(), $valueGenerators)
            );
        }
    }
}