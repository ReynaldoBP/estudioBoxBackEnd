<?php

namespace App\Entity;

use App\Repository\InfoCuponHistorialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoCuponHistorial
 * @ORM\Table(name="INFO_CUPON_HISTORIAL")
 * @ORM\Entity(repositoryClass="App\Repository\InfoCuponHistorialRepository")
 */
class InfoCuponHistorial
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_CUPON_HISTORIAL", type="integer")
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
    * @var InfoEmpresa
    *
    * @ORM\ManyToOne(targetEntity="InfoEmpresa")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="ID_EMPRESA")
    * })
    */
    private $EMPRESA_ID;

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
     * Set CUPONID
     *
     * @param \App\Entity\InfoCupon $CUPONID
     *
     * @return InfoCuponHistorial
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
     * @return InfoCuponHistorial
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
     * Set EMPRESAID
     *
     * @param \App\Entity\InfoEmpresa $EMPRESAID
     *
     * @return InfoCuponHistorial
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
