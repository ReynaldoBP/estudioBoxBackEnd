<?php

namespace App\Entity;

use App\Repository\InfoPublicidadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPublicidad
 * @ORM\Table(name="INFO_PUBLICIDAD")
 * @ORM\Entity(repositoryClass=InfoPublicidadRepository::class)
 */
class InfoPublicidad
{
   /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_PUBLICIDAD",type="integer")
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
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_CREACION;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $USR_MODIFICACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_MODIFICACION;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="integer")
     */
    private $TIEMPO;

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

    /**
    * @var InfoArea
    *
    * @ORM\ManyToOne(targetEntity="InfoArea")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="AREA_ID", referencedColumnName="ID_AREA")
    * })
    */
    private $AREA_ID;

    /**
    * @var InfoEncuesta
    *
    * @ORM\ManyToOne(targetEntity="InfoEncuesta")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="ENCUESTA_ID", referencedColumnName="ID_ENCUESTA")
    * })
    */
    private $ENCUESTA_ID;

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

    public function setUSRCREACION(?string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(?\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

    public function getUSRMODIFICACION(): ?string
    {
        return $this->USR_MODIFICACION;
    }

    public function setUSRMODIFICACION(?string $USR_MODIFICACION): self
    {
        $this->USR_MODIFICACION = $USR_MODIFICACION;

        return $this;
    }

    public function getFEMODIFICACION(): ?\DateTimeInterface
    {
        return $this->FE_MODIFICACION;
    }

    public function setFEMODIFICACION(?\DateTimeInterface $FE_MODIFICACION): self
    {
        $this->FE_MODIFICACION = $FE_MODIFICACION;

        return $this;
    }

    public function getESTADO(): ?string
    {
        return $this->ESTADO;
    }

    public function setESTADO(string $ESTADO): self
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

    public function getAREAID(): ?infoArea
    {
        return $this->AREA_ID;
    }

    public function setAREAID(?infoArea $AREA_ID): self
    {
        $this->AREA_ID = $AREA_ID;

        return $this;
    }

    public function getENCUESTAID(): ?infoEncuesta
    {
        return $this->ENCUESTA_ID;
    }

    public function setENCUESTAID(?infoEncuesta $ENCUESTA_ID): self
    {
        $this->ENCUESTA_ID = $ENCUESTA_ID;

        return $this;
    }

    public function getTIEMPO(): ?int
    {
        return $this->TIEMPO;
    }

    public function setTIEMPO(int $TIEMPO): self
    {
        $this->TIEMPO = $TIEMPO;

        return $this;
    }
}
