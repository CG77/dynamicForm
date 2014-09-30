<?php


namespace Novactive\EzPublishFormGeneratorBundle\Storage;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\ProcessBuilder;

/**
 * File storage service
 *
 * @author m.monsang <m.monsang@novactive.com>
 */
class FileService {

    protected $allowedExtensions = array();

    protected $uploadDir = null;

    protected $webDir = null;

    protected $filesystem;

    protected $pdfFormFillerJar;

    protected $pdfFormFillerFont;

    protected $legacyDir;

    protected $mdsCsv;

    function __construct( $allowedExtensions, $uploadDir, $webDir, $pdfFormFillerJar, $pdfFormFillerFont, $legacyDir, $mdsCsv )
    {
        $this->uploadDir = $uploadDir;
        $this->allowedExtensions = $allowedExtensions;
        $this->filesystem = new Filesystem();
        $this->webDir = $webDir;
        $this->pdfFormFillerJar = $pdfFormFillerJar;
        $this->pdfFormFillerFont = $pdfFormFillerFont;
        $this->legacyDir = $legacyDir;
        $this->mdsCsv = $mdsCsv;

        try {
            if (!$this->filesystem->exists($this->uploadDir)) {
                $this->filesystem->mkdir($this->uploadDir);
            }
        } catch (IOException $ex) {
            throw new \Exception(sprintf("Can not create directory [%s] : %s", $this->uploadDir, $ex->getMessage()));
        }


    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function checkExtension(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $this->allowedExtensions)) {
            return false;
        }
        return true;
    }

    /**
     * @param UploadedFile $file
     * @param $directoryPrefix
     * @return array
     */
    public function store(UploadedFile $file, $directoryPrefix)
    {
        $originalFilename = $file->getClientOriginalName();
        // check extension;

        $directory = $this->uploadDir . DIRECTORY_SEPARATOR . $directoryPrefix;
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory);
        }

        $hash = sha1(rand() . '-' . $originalFilename);
        $fileName = $hash . '.' . $file->getClientOriginalExtension();

        $directory = $this->uploadDir . DIRECTORY_SEPARATOR . $directoryPrefix;
        $relativePath = $directoryPrefix . DIRECTORY_SEPARATOR . $fileName;
        $file->move($directory, $fileName);

        return array(
            'name' => $originalFilename,
            'path' => $relativePath,
            'metadata' => array(
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getClientSize()
            )
        );

    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * @param $data
     * @return string
     */
    public function getFileInfos($data)
    {
        return array(
            'name' => $data['name'],
            'path' => $this->webDir . '/' . $data['path']
        );
    }

    public function generatePdf($values,$template,$prefix,$suffix){
        $output = "";

        foreach( $values as $identifier => $value ){
            $output .= $identifier . " " . $value . PHP_EOL;
        }

        $processBuilder = new ProcessBuilder(
            array(
                "java",
                "-jar",
                $this->pdfFormFillerJar,
                $this->legacyDir.'/'.$template,
                "-font",
                $this->pdfFormFillerFont,
                "-flatten",
                $this->uploadDir.'/'.$prefix.'.'.$suffix
            )
        );
        $process = $processBuilder->getProcess();
        $process->setEnv(array( "LANG" => "fr_FR.UTF-8" ));
        $process->setStdin($output);
        $process->run();

        if ( !$process->isSuccessful() )
        {
            throw new \Exception( $process->getErrorOutput() );
        }else{
            return $this->uploadDir.'/'.$prefix.'.'.$suffix;
        }
    }

    public function getCommuneFromCsv($value){
        $results = array();
        $handle = fopen($this->mdsCsv,"r");
        if( $handle !== FALSE ){
            $row = 0;
            while (( $data = fgetcsv($handle, null, ";" )) !== FALSE ) {
                if(strstr(strtolower($data[1]),strtolower($value))){
                    $results[$row]['zipcode'] = $data[0];
                    $results[$row]['commune'] = $data[1];
                    $results[$row]['mds'] = $data[2];
                    $results[$row]['email'] = $data[3];
                    if(count($results) >= 10){
                        break;
                    }
                }
                $row++;
            }
            fclose( $handle );
        }
        return $results;
    }

    public function getMailsFromCsv($value){
        $handle = fopen($this->mdsCsv,"r");
        if($handle !== FALSE){
            while(( $data = fgetcsv( $handle, null, ";" )) !== FALSE){
                if($data[0] === $value){
                    return $data[3];
                }
            }
        }
        return false;
    }


}