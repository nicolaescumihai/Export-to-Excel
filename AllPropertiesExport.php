<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;


class AllPropertiesExport implements  FromCollection, WithMapping, WithHeadings
{
    protected $extended = [];

    protected $heading = [];

    public function __construct ()
    {

        $this->createCollection();

    }

    public function createCollection()
    {

        $tableExtendedProperties = DB::select
                (
                    '
                    SELECT 
                    t.[name] AS TableName,
                    c.[name] AS ColumnName,
                    c.column_id,
                    ep.ExtendedPropertyName,
                    ep.ExtValue
                FROM sys.columns AS c
                                
                    INNER JOIN sys.tables AS t
                    ON t.object_id = c.object_id 
                                
                    INNER JOIN sys.schemas AS s
                    ON s.schema_id = t.schema_id 
                                
                    INNER JOIN sys.types AS typ
                    ON typ.system_type_id = c.system_type_id 
                    AND typ.user_type_id = c.user_type_id 
                                
                    OUTER APPLY 
                        (
                            SELECT ep.[name] AS ExtendedPropertyName,
                        CAST(ep.[value] as nvarchar(254))  AS ExtValue
                    FROM sys.extended_properties AS ep
                    WHERE ep.major_id = c.object_id 
                        AND ep.minor_id = c.column_id
                        ) AS ep
                ORDER BY  t.[name], c.[name]
                    '
                );
// dd($tableExtendedProperties);
        $tableProperties = DB::select
                (
                    '
                    SELECT  
                    ORDINAL_POSITION, TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT,
                    CHARACTER_MAXIMUM_LENGTH, CHARACTER_SET_NAME, COLLATION_NAME
                FROM
                INFORMATION_SCHEMA.COLUMNS
                ORDER BY TABLE_NAME
                    '
                );

                //se itereaza prin ambele array-uri rezultate din query 
               
        foreach($tableProperties as $tableProperty
        ){
            
            foreach($tableExtendedProperties as $extendeProperty)
            {
                //apoi se face verificarea ca cele 2 campuri sa fie identice si egale     
                if($tableProperty->TABLE_NAME === $extendeProperty->TableName && 
                    $tableProperty->COLUMN_NAME === $extendeProperty->ColumnName )
                {
                    //se pune intr-o variabila valoarea key campului respectiv       
                    $ext = $extendeProperty->ExtendedPropertyName;
                        //se face verificarea daca aceasta valoare exista     
                        if($ext)
                        {

                            if(!in_array($ext, $this->extended))
                            {

                                array_push($this->extended, $ext);
                            }
                            //se adauga la primul excel valorile din cel de al 2-lea excel
                            $tableProperty->$ext= $extendeProperty->ExtValue;
                             
                        }
                }
                
            }

            // dd($tableProperty);
        }
        
       $this->collection = collect($tableProperties);
    }

    public function collection()
    {
       return $this->collection;
  
    }

    
    public function map($row): array
    {
        $column = [];

        foreach($this->extended as $properti)
        {
       
           if(property_exists($row, $properti))
            {
               array_push($column, $row->$properti);
            }else
            {
                array_push($column, '');
            }
        }

        $array =
        [
            $row->ORDINAL_POSITION,
            $row->TABLE_NAME,
            $row->COLUMN_NAME,
            $row->DATA_TYPE,
            $row->IS_NULLABLE,
            $row->COLUMN_DEFAULT,
            $row->CHARACTER_MAXIMUM_LENGTH,
            $row->CHARACTER_SET_NAME,
            $row->COLLATION_NAME,
        ];

        $array = array_merge($array, $column);
       
        
        return $array;
    }

    public function headings(): array
    {
        
        $heading = [];

        foreach($this->extended as $head)
        {
            if(!in_array($head, $heading))
            {
                array_push($heading, $head);
            }
        }

        $array =
        [
            'Column Id',
            'Table Name',
            'Column Name',
            'Data Type',
            'Is Nullable',
            'Column Default',
            'Char maximum Length',
            'Char Set Name',
            'Collation Name'
        ];

        $array = array_merge($array, $heading);

        return $array;
    }
}
