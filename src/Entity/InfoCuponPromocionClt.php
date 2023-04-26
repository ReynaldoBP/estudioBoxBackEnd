<?php

namespace App\Entity;

use App\Repository\InfoCuponPromocionCltRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoCuponPromocionClt
 * @ORM\Table(name="INFO_CUPON_PROMOCION_CLT")
 * @ORM\Entity(repositoryClass="App\Repository\InfoCuponPromocionClt")
 */
class InfoCuponPromocionClt
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CUPON_PROMOCION_CLT", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var InfoCupon
    *
    * @ORM\ManyToOne(targetEntity="InfoCupon")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CUPON_ID", referencedColumnName="ID_CUPON")
    * })
    */
    private $CUPON_ID;

    /**
    * @var InfoCliente
    *
    * @ORM\ManyToOne(targetEntity="InfoCliente")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CLIENTE_ID", referencedColumnName="ID_CLIENTE")
    * })
    */
    private $CLIENTE_ID;

    /**
    * @var InfoPromocion
    *
    * @ORM\ManyToOne(targetEntity="InfoPromocion")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="PROMOCION_ID", referencedColumnName="ID_PROMOCION")
    * })
    */
    private $PROMOCION_ID;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_VIGENCIA;

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
     * Set CUPONID
     *
     * @param \App\Entity\InfoCupon $CUPONID
     *
     * @return InfoCuponPromocionClt
     */
    public function setCUPONID(\App\Entity\InfoCupon $CUPONID = null)
    {
        $this->CUPON_ID = $CUPONID;

        return $this;
    }

    /**
     * Get CUPONID
     *
     * @return \App\Entity\InfoCupon
     */
    public function getCUPONID()
    {
        return $this->CUPON_ID;
    }

    /**
     * Set CLIENTEID
     *
     * @param \App\Entity\InfoCliente $CLIENTEID
     *
     * @return InfoCuponPromocionClt
     */
    public function setCLIENTEID(\App\Entity\InfoCliente $CLIENTEID = null)
    {
        $this->CLIENTE_ID = $CLIENTEID;

        return $this;
    }

    /**
     * Get CLIENTEID
     *
     * @return \App\Entity\InfoCliente
     */
    public function getCLIENTEID()
    {
        return $this->CLIENTE_ID;
    }

    /**
     * Set PROMOCIONID
     *
     * @param \App\Entity\InfoPromocion $PROMOCIONID
     *
     * @return InfoCuponPromocionClt
     */
    public function setPROMOCIONID(\App\Entity\InfoPromocion $PROMOCIONID = null)
    {
        $this->PROMOCION_ID = $PROMOCIONID;

        return $this;
    }

    /**
     * Get PROMOCIONID
     *
     * @return \App\Entity\InfoPromocion
     */
    public function getPROMOCIONID()
    {
        return $this->PROMOCION_ID;
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

    public function getFEVIGENCIA(): ?\DateTimeInterface
    {
        return $this->FE_VIGENCIA;
    }

    public function setFEVIGENCIA(\DateTimeInterface $FE_VIGENCIA): self
    {
        $this->FE_VIGENCIA = $FE_VIGENCIA;

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
