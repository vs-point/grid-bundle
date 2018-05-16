<?php

namespace PedroTeixeira\Bundle\GridBundle\Grid\Filter\Operator;

/**
 * Comparison
 */
class ConcatComparison extends OperatorAbstract
{
    /**
     * @var string
     */
    protected $comparisonType = 'contain';
    
    private function getConcatIndex() {
        return 'CONCAT('.$this->getIndex().')';
    }

    /**
     * @param string|array $value
     */
    public function execute($value)
    {
        $parameter = $value;
        $queryBuilder = $this->getQueryBuilder();
        $expression = $this->getQueryBuilder()->expr();
        
        switch ($this->getComparisonType()) {
            case 'begin_with':
                $where = $expression->like($this->getConcatIndex(), ":{$this->getIndexClean()}");
                $parameter = $value . '%';
                break;

            case 'not_begin_with':
                $where = $this->getConcatIndex() . " NOT LIKE :{$this->getIndexClean()}";
                $parameter = $value . '%';
                break;

            case 'end_with':
                $where = $expression->like($this->getConcatIndex(), ":{$this->getIndexClean()}");
                $parameter = '%' . $value;
                break;

            case 'not_end_with':
                $where = $this->getConcatIndex() . " NOT LIKE :{$this->getIndexClean()}";
                $parameter = '%' . $value;
                break;

            case 'not_contain':
                $where = $this->getConcatIndex() . " NOT LIKE :{$this->getIndexClean()}";
                $parameter = '%' . $value . '%';
                break;

            case 'contain':

            default:
                $where = $expression->like($this->getConcatIndex(), ":{$this->getIndexClean()}");
                $parameter = '%' . $value . '%';
        }

        if ($this->getWhere() == 'OR') {
            $queryBuilder->orWhere($where);
        } else {
            $queryBuilder->andWhere($where);
        }

        if (isset($parameter)) {
            $queryBuilder->setParameter($this->getIndexClean(), $parameter);
        }
    }

    /**
     * @param string $comparisonType
     *
     * @return Comparison
     */
    public function setComparisonType($comparisonType)
    {
        $this->comparisonType = $comparisonType;

        return $this;
    }

    /**
     * @return string
     */
    public function getComparisonType()
    {
        return $this->comparisonType;
    }
}
