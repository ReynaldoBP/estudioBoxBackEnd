<?php

namespace App\Entity;

use App\Repository\InfoReporteArchivoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoReporteArchivo
 * @ORM\Table(name="INFO_REPORTE_ARCHIVO")
 * @ORM\Entity(repositoryClass=InfoReporteArchivoRepository::class)
 */
class InfoReporteArchivo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_RELACION",type="integer")
     */
    private $id;

    /**
    * @var InfoReporte
    *
    * @ORM\ManyToOne(targetEntity="InfoReporte")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="REPORTE_ID", referencedColumnName="ID_REPORTE")
    * })
    */
    private $reporte;

    /**
    * @var InfoArchivo
    *
    * @ORM\ManyToOne(targetEntity="InfoArchivo")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="ARCHIVO_ID", referencedColumnName="ID_ARCHIVO")
    * })
    */
    private $archivo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getREPORTE(): ?InfoReporte
    {
        return $this->reporte;
    }

    public function setREPORTE(?InfoReporte $reporte): self
    {
        $this->reporte = $reporte;

        return $this;
    }

    public function getARCHIVO(): ?InfoArchivo
    {
        return $this->archivo;
    }

    public function setARCHIVO(?InfoArchivo $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }
}
