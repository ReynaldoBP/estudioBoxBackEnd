<?php

namespace App\Entity;

use App\Repository\InfoReporteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoReporte
 * @ORM\Table(name="INFO_REPORTE")
 * @ORM\Entity(repositoryClass=InfoReporteRepository::class)
 */
class InfoReporte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_REPORTE",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TITULO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DESCRIPCION;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_CREACION;
    
    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $ESTADO;

    /**
    * @var InfoEmpresa
    *
    * @ORM\ManyToOne(targetEntity="InfoEmpresa")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="ID_EMPRESA")
    * })
    */
    private $EMPRESA_ID;

    /**
    * @var InfoSucursal
    *
    * @ORM\ManyToOne(targetEntity="InfoSucursal")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="SUCURSAL_ID", referencedColumnName="ID_SUCURSAL")
    * })
    */
    private $SUCURSAL_ID;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTITULO(): ?string
    {
        return $this->TITULO;
    }

    public function setTITULO(string $TITULO): self
    {
        $this->TITULO = $TITULO;

        return $this;
    }

    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(?string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    public function getUSRCREACION(): ?string
    {
        return $this->USR_CREACION;
    }

    public function setUSRCREACION(string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

    public function getESTADO(): ?string
    {
        return $this->ESTADO;
    }

    public function setESTADO(?string $ESTADO): self
    {
        $this->ESTADO = $ESTADO;

        return $this;
    }

    public function getEMPRESAID(): ?infoEmpresa
    {
        return $this->EMPRESA_ID;
    }

    public function setEMPRESAID(?infoEmpresa $EMPRESA_ID): self
    {
        $this->EMPRESA_ID = $EMPRESA_ID;

        return $this;
    }

    public function getSUCURSALID(): ?infoSucursal
    {
        return $this->SUCURSAL_ID;
    }

    public function setSUCURSALID(?infoSucursal $SUCURSAL_ID): self
    {
        $this->SUCURSAL_ID = $SUCURSAL_ID;

        return $this;
    }
}
