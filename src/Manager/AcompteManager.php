<?php

namespace App\Manager;

use App\Entity\Company;
use SSH\MsJwtBundle\Utils\MyTools;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use SSH\MsJwtBundle\Manager\ExceptionManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Entity\Acompte;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Description of AvtManager
 *
 * @author mariaDebawi
 */
class AcompteManager extends AbstractManager
{

    /**
     *  @var string
     */
    private $code;

    /**
     *  @var string
     */
    private $companyCode;

    /**
     *
     * @var Company
     */
    private $company;

    /**
     *
     * @var acompte
     */
    private $acompte;

    /**
     * @var CompanyManager
     */
    private $companyManager;

    public function __construct(
        Registry $entityManager,
        ExceptionManager $exceptionManager,
        RequestStack $requestStack,
        CompanyManager $companyManager
    )
    {
        $this->companyManager = $companyManager;
        parent::__construct($entityManager, $exceptionManager, $requestStack);
    }

    /**
     * AbstractManager initializer.
     */
    public function init($settings = [])
    {
        parent::setSettings($settings);
        $this->company = null;
        $this->acompte = null;
        $this->userCaller = $this->request->get('userCaller');
        $this->companyUserCaller = $this->request->get('companyUserCaller');

        if ($this->companyUserCaller instanceof User) {
            $this->company = $this->companyUserCaller->getCompany();
        }


        if ($this->getCode()) {
            // find existing job_type
            $this->acompte = $this->apiEntityManager
                ->getRepository(Acompte::class)
                ->findOneBy(['code' => $this->getCode()]);

            if (!($this->acompte instanceof Acompte)) {
                $this->exceptionManager->throwNotFoundException('no_acompte_found');
            }
            $this->company = $this->acompte->getCompany();
        }

        if (!$this->company && $this->getCompanyCode()) {
            $this->company = $this->companyManager
                ->init(['code' => $this->getCompanyCode()])
                ->getCompany();
        }



        return $this;
    }

    /**
     * Setter  code.
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Getter code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Setter  companyCode.
     *
     * @param string $companyCode
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;

        return $this;
    }

    /**
     * Getter code.
     *
     * @return string
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * list
     *
     * @return array
     */
    public function acomptes()
    {


        $filters = (array) $this->request->get('Acomptes');

        if ($this->company instanceof Company) {
            $filters['company'] = $this->company->getId();
        }

        $acomptes = $this->apiEntityManager
            ->getRepository(Acompte::class)
            ->getByFilters($filters);

        $total = MyTools::getValueFromResultSet($acomptes, 'tolal');

        return ['data' => MyTools::jtablePaginatorRows($acomptes, $filters['index'], $filters['size'], $total)];
    }

    /**
     * Get acompte
     *
     * @return Acompte
     */
    public function getAcompte($toArray = false, $toSnake = true)
    {
        if ($toArray) {
            return $this->acompte->toArray($toSnake);
        }

        return $this->acompte;
    }

    public function acomptesChoice()
    {

        $filters = ['index' => -1, 'search' => $this->request->get('search')];
        if ($this->company instanceof Company) {
            $filters['company'] = $this->company->getId();
        }

        $acompte = $this->apiEntityManager
            ->getRepository(Acompte::class)
            ->getByFilters($filters);

        return ['data' => array_values(MyTools::getArrayFromResultSet($acompte, 'code', ['code', 'longname']))];
    }

    /**
     * Update
     *
     *
     * @return array
     */
    public function set()
    {
        $acompte = (array) $this->request->get('Acompte');

        $this->validateUnicity(Acompte::class, 'value', ['value' => $acompte['value']], $this->acompte);

        $acompte['company'] = $this->company;

        if (is_a($this->acompte, Acompte::class)) {
            return $this->updateObject($this->acompte, $acompte);
        }

        $this->acompte = $this->insertObject($acompte, Acompte::class);
        return ['data' => [
            'messages' => 'create_success',
            'code' => $this->acompte->getCode(),
        ]];
    }

}
