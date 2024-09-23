<?php

namespace App\Entity;

use App\Repository\InfoBitacoraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoBitacora
 * @ORM\Table(name="INFO_BITACORA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoBitacoraRepository")
 */
class InfoBitacora
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_BITACORA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ACCION;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $MODULO;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $REFERENCIA_ID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $REFERENCIA_VALOR;

    /**
    * @var InfoUsuario
    *
    * @ORM\ManyToOne(targetEntity="InfoUsuario")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="USUARIO_ID", referencedColumnName="ID_USUARIO")
    * })
    */
    private $USUARIO_ID;

    /**
     * @ORM\Column(type="string")
     */
    private $FE_CREACION;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getACCION(): ?string
    {
        return $this->ACCION;
    }

    public function setACCION(string $ACCION): self
    {
        $this->ACCION = $ACCION;

        return $this;
    }

    public function getMODULO(): ?string
    {
        return $this->MODULO;
    }

    public function setMODULO(string $MODULO): self
    {
        $this->MODULO = $MODULO;

        return $this;
    }

    public function setREFERENCIAID(int $REFERENCIAID): self
    {
        $this->REFERENCIA_ID = $REFERENCIAID;

        return $this;
    }

    public function getREFERENCIAID(): ?int
    {
        return $this->REFERENCIA_ID;
    }

    public function getREFERENCIA_VALOR(): ?string
    {
        return $this->REFERENCIA_VALOR;
    }

    public function setREFERENCIA_VALOR(string $REFERENCIA_VALOR): self
    {
        $this->REFERENCIA_VALOR = $REFERENCIA_VALOR;

        return $this;
    }

    /**
     * Set USUARIOID
     *
     * @param \App\Entity\InfoUsuario $USUARIOID
     *
     * @return InfoBitacora
     */
    public function setUSUARIOID(\App\Entity\InfoUsuario $USUARIOID = null)
    {
        $this->USUARIO_ID = $USUARIOID;

        return $this;
    }

    /**
     * Get USUARIOID
     *
     * @return \App\Entity\InfoUsuario
     */
    public function getUSUARIOID()
    {
        return $this->USUARIO_ID;
    }

    public function getFECREACION(): ?string
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(string $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }
}
