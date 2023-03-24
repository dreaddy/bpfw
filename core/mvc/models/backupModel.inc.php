<?php /** @noinspection PhpUnused */

class BackupModel extends BpfwModel
{

    var bool $translateDbModelLabels=true;
    function __construct($values = null, $autocompleteVariables = true)
    {


        parent::__construct($values, $autocompleteVariables);


        $this->minUserrankForEdit = USERTYPE_INVALID;
        $this->minUserrankForShow = USERTYPE_ADMIN;
        $this->minUserrankForAdd = USERTYPE_INVALID;
        $this->minUserrankForDelete = USERTYPE_ADMIN;
        $this->minUserrankForDuplicate = USERTYPE_INVALID;

        // $this->ignoreOnSync = true;

        $this->showdata = true;


        $this->ignoreOnImportExport = true; // do not touch this table im import/export
    }

    /**
     * Summary of DeleteEntry
     * @param int $key primary key
     * @param bool $temptable
     * @throws Exception
     */
    public function DeleteEntry(int $key, $temptable = false): bool
    {

        if (!$temptable) {
            $this->deleteBackupFolder($key);
        }

        return parent::DeleteEntry($key, $temptable);


    }

    /**
     * @throws Exception
     */
    function deleteBackupFolder($backupid): bool
    {

        $info = $this->GetEntry($backupid);

        if (!empty($info->pathBackup) && file_exists($info->pathBackup)) {
            bpfw_delete_dir($info->pathBackup);
            return true;
        }

        return false;

    }

    /**
     * gibt Tabellennamen zurück.
     * @return string
     */
    public function GetTableName(): string
    {
        return "backup";
    }

    public function GetTitle(): string
    {
        return __("Backups");
    }

    /**
     * gibt Db Model zurück. Darin beschrieben sind Typ und Name und einige Details der in der Datenbank vorkommenden Felder
     * @return array
     * @throws Exception
     * @throws Exception
     */
    protected function loadDbModel(): array
    {

        $this->addPrimaryKey("backupId", "ID", BpfwDbFieldType::TYPE_INT, 'text', array(LISTSETTING::HIDDENONLIST => false));

        $this->addDateTimePicker("backupdate", "Date", array(LISTSETTING::HIDDENONLIST => false));

        $this->addTextFieldNumeric("files", "Files");

        $this->addTextField("pathJson", "DB folder", 'default', array(LISTSETTING::HIDDENONLIST => true));

        $this->addTextField("pathFiles", "Path files", 'default', array(LISTSETTING::HIDDENONLIST => true));

        $this->addTextField("pathBackup", "Backup directory");

        $this->addTextField("size", "Filesize");

        $this->addComboBoxIntkeybased("backuptype", "Type", new EnumHandlerArray(array(0 => "Full backup", 1 => "Database only")));

        return $this->dbModel;

    }

}