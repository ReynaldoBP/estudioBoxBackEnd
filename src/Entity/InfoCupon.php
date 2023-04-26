<?php

namespace App\Entity;

use App\Repository\InfoCuponRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoCupon
 * @ORM\Table(name="INFO_CUPON")
 * @ORM\Entity(repositoryClass="App\Repository\InfoCuponRepository")
 */
class InfoCupon
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CUPON", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var AdmiTipoCupon
    *
    * @ORM\ManyToOne(targetEntity="AdmiTipoCupon")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="TIPO_CUPON_ID", referencedColumnName="ID_TIPO_CUPON")
    * })
    */
    private $TIPO_CUPON_ID;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $CUPON;

    /**
     * @var int
     *
     * @ORM\Column(name="DIA_VIGENTE", type="integer", nullable=true)
     */
    private $DIA_VIGENTE;

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
     * Set TIPOCUPONID
     *
     * @param \App\Entity\AdmiTipoCupon $TIPOCUPONID
     *
     * @return InfoCupon
     */
    public function setTIPOCUPONID(\App\Entity\AdmiTipoCupon $TIPOCUPONID = null)
    {
        $this->TIPO_CUPON_ID = $TIPOCUPONID;

        return $this;
    }

    /**
     * Get TIPOCUPONID
     *
     * @return \App\Entity\AdmiTipoCupon
     */
    public function getTIPOCUPONID()
    {
        return $this->TIPO_CUPON_ID;
    }

    public function getCUPON(): ?string
    {
        return $this->CUPON;
    }

    public function setCUPON(string $CUPON): self
    {
        $this->CUPON = $CUPON;

        return $this;
    }

    public function getDIAVIGENTE(): ?int
    {
        return $this->DIA_VIGENTE;
    }

    public function setDIAVIGENTE(int $DIA_VIGENTE): self
    {
        $this->DIA_VIGENTE = $DIA_VIGENTE;

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
