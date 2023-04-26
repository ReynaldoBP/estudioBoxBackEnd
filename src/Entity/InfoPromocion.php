<?php

namespace App\Entity;

use App\Repository\InfoPromocionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPromocion
 * @ORM\Table(name="INFO_PROMOCION")
 * @ORM\Entity(repositoryClass="App\Repository\InfoPromocionRepository")
 */
class InfoPromocion
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_PROMOCION", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var AdmiTipoPromocion
    *
    * @ORM\ManyToOne(targetEntity="AdmiTipoPromocion")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="TIPO_PROMOCION_ID", referencedColumnName="ID_TIPO_PROMOCION")
    * })
    */
    private $TIPO_PROMOCION_ID;

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
     * @ORM\Column(type="string", length=255)
     */
    private $DESCRIPCION;

    /**
     * @var int
     *
     * @ORM\Column(name="CANT_DIAS_VIGENCIA", type="integer", nullable=true)
     */
    private $CANTDIASVIGENCIA;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_CREACION;

    /**
     * @var string
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", length=255, nullable=true)
     */
    private $USR_MODIFICACION;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=true)
     */
    private $FE_MODIFICACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set TIPOPROMOCIONID
     *
     * @param \App\Entity\AdmiTipoPromocion $TIPOPROMOCIONID
     *
     * @return InfoPromocion
     */
    public function setTIPOPROMOCIONID(\App\Entity\AdmiTipoPromocion $TIPOPROMOCIONID = null)
    {
        $this->TIPO_PROMOCION_ID = $TIPOPROMOCIONID;

        return $this;
    }

    /**
     * Get TIPOPROMOCIONID
     *
     * @return \App\Entity\AdmiTipoPromocion
     */
    public function getTIPOPROMOCIONID()
    {
        return $this->TIPO_PROMOCION_ID;
    }

    /**
     * Set EMPRESAID
     *
     * @param \App\Entity\InfoEmpresa $EMPRESAID
     *
     * @return InfoPromocion
     */
    public function setEMPRESAID(\App\Entity\InfoEmpresa $EMPRESAID = null)
    {
        $this->EMPRESA_ID = $EMPRESAID;

        return $this;
    }

    /**
     * Get EMPRESAID
     *
     * @return \App\Entity\InfoEmpresa
     */
    public function getEMPRESAID()
    {
        return $this->EMPRESA_ID;
    }

    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    public function getCANTDIASVIGENCIA(): ?int
    {
        return $this->CANTDIASVIGENCIA;
    }

    public function setCANTDIASVIGENCIA(int $CANTDIASVIGENCIA): self
    {
        $this->CANTDIASVIGENCIA = $CANTDIASVIGENCIA;

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
}
