<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Mindsoft',
            'idNumber' => '1791888944001',
            'contactEmail' => 'info@mindsoft.biz',
            'phone' => '+593984258842',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Hernandez de Jirón y Av. América',
        ]);
        Company::create([
            'name' => 'AGLOMERADOS COTOPAXI S.A.',
            'idNumber' => '0590028665001',
            'contactEmail' => 'grp.contabilidad@cotopaxi.com.ec',
            'phone' => '023985200',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Lasso, Panamericana Norte Km. 21 desde Latacunga',
        ]);
        Company::create([
            'name' => 'AMALGAMA CIA LTDA',
            'idNumber' => '1792774780001',
            'contactEmail' => 'amalgamatoys@gmail.com',
            'phone' => '+5930998569871',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. de los Shyris n34-40 y Rep del Salvador',
        ]);
        Company::create([
            'name' => 'ASOCIACIÓN CULTURAL ACADEMIA COTOPAXI',
            'idNumber' => '1790105083001',
            'contactEmail' => 'msuarez@cotopaxi.k12.ec',
            'phone' => '023823270',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'De las Higuerrillas E16-102 y Alondras (Monteserrín)',
        ]);
        Company::create([
            'name' => 'AUDITORPOOL ASESORES GRUPO AVANT',
            'idNumber' => '1792478146001',
            'contactEmail' => 'gamapublicitaria@gmail.com',
            'phone' => '022233841',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'REINA VICTORIA N25-33 Y AV COLON',
        ]);
        Company::create([
            'name' => 'Belen Baquero',
            'idNumber' => '1713579579',
            'contactEmail' => 'belenbaquerocardenas@gmail.com',
            'phone' => '0995466241',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Monteserrin',
        ]);
        Company::create([
            'name' => 'BENJAMIN ORTIZ Y ASOCIADOS CIA. LTDA.',
            'idNumber' => '1791919661001',
            'contactEmail' => 'monica.izurieta@boa.ec',
            'phone' => '022448460',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Guayas NE 3-112 y Av. Amazonas | Ed. Torre Centre',
        ]);
        Company::create([
            'name' => 'Bruno Vassari',
            'idNumber' => '1792033454001',
            'contactEmail' => 'contabilidad@brunovassari.com.ec',
            'phone' => '022261228',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Naciones Unidas e Iñaquito',
        ]);
        Company::create([
            'name' => 'Cámara Ecuatoriano Británica',
            'idNumber' => '1790977544001',
            'contactEmail' => 'info.uio@britcham.com.ec',
            'phone' => '022250883',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Amazonas y República, Edificio Las Cámaras, piso 9',
        ]);
        Company::create([
            'name' => 'Centro Internacional de Arbitraje y Mediación (CIAM)',
            'idNumber' => '1792149215001',
            'contactEmail' => 'info@ciam.com.ec',
            'phone' => '2452-500. Ext 110',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Amazonas y Av. República, Edificio Las Cámaras, Piso 9.',
        ]);
        Company::create([
            'name' => 'Centros Comerciales del Ecuador C.A.',
            'idNumber' => '1790009378001',
            'contactEmail' => 'facturacionproveedores@cci.com.ec',
            'phone' => '6017250',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Amazonas N36-152 y Naciones Unidas',
        ]);
        Company::create([
            'name' => 'COMAFORS',
            'idNumber' => '1791426975001',
            'contactEmail' => 'administracion@comafors.org',
            'phone' => '0995003434',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'N33B Inglaterra E3-263 Y Av. Amazonas, Edif Centro Ejecutivo, piso 6 Oficina 602',
        ]);
        Company::create([
            'name' => 'Corporación Valarezo Noboa',
            'idNumber' => '1791770889001',
            'contactEmail' => 'torresdelcastilloec@gmail.com',
            'phone' => '022467076',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => '12 de Octubre N24-359 y Vaquerizo Moreno Plaza Corporativa Torres del Castillo',
        ]);
        Company::create([
            'name' => 'Cosideco Cia. Ltda.',
            'idNumber' => '1790502015001',
            'contactEmail' => 'contabilidad2@cosideco.com',
            'phone' => '022229818',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. 6 de Diciembre N31-89 y Whymper, Edif. Cosideco, Oficina C1',
        ]);
        Company::create([
            'name' => 'CREARTI',
            'idNumber' => '1792468396001',
            'contactEmail' => 'presidencia@deleyexpress.com',
            'phone' => '022283651',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Pasaje carlos Ibarra n1-76 y 10 de agosto ofi 303 304',
        ]);
        Company::create([
            'name' => 'DE LA PAZ CALISTO FRANCISCO JOSE ALEJANDRO',
            'idNumber' => '1703139475001',
            'contactEmail' => 'gerencia@mueblesforma.com.ec',
            'phone' => '+593 99 962 5728',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Tupigachi',
        ]);
        Company::create([
            'name' => 'Desarrollos inmobiliarios Trabahaq S.A',
            'idNumber' => '1792315247001',
            'contactEmail' => 'financiero@trabahaq.com',
            'phone' => '022269500',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'ANTONIO DE ULLOA Y PEDRO BEDON Edificio Kamay',
        ]);
        Company::create([
            'name' => 'Diego Xavier Leiva Vargas',
            'idNumber' => '1712219151001',
            'contactEmail' => 'facturaslasevillana@gmail.com',
            'phone' => '032230402',
            'state' => 'Cotopaxi',
            'city' => 'Latacunga',
            'address' => 'José Guango Bajo, laygua vía a la piedra Colorada',
        ]);
        Company::create([
            'name' => 'Dosakin S.A.',
            'idNumber' => '1792340195001',
            'contactEmail' => 'dosakinecu@hotmail.con',
            'phone' => '023430945',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Pedro Porras y Julio Viteri. Pomasqui',
        ]);
        Company::create([
            'name' => 'Dr. Juan Roldán Crespo',
            'idNumber' => '1704131711001',
            'contactEmail' => 'anavasquez@anamariavasquez.com',
            'phone' => '022242072',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'San Gabriel y Nicolás Arteta',
        ]);
        Company::create([
            'name' => 'DRA Ana María Vasquez García',
            'idNumber' => '1305239343001',
            'contactEmail' => 'anavasquez@anamariavasquez.com',
            'phone' => '0999710081',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Torres Medicas 2 5to Piso Oficina 501',
        ]);
        Company::create([
            'name' => 'ECLINIQ SAS',
            'idNumber' => '1793075150001',
            'contactEmail' => 'info@ecliniq.com',
            'phone' => '+593 984921416',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Juan de Ascaray número 64 y Av. del Rancho',
        ]);
        Company::create([
            'name' => 'EDIFIER Cia Ltda',
            'idNumber' => '1792218306001',
            'contactEmail' => 'juancarlos@inmoweb.com.ec',
            'phone' => '6017655',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Shyris y Suecia esq. Edificio Renazzo Plaza oficina 202',
        ]);
        Company::create([
            'name' => 'Edison Jarrin',
            'idNumber' => '1707599013',
            'contactEmail' => 'ejarrinc@hotmail.com',
            'phone' => '0998023526',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Mirasierra',
        ]);
        Company::create([
            'name' => 'Federación de Cámaras Binacionales del Ecuador',
            'idNumber' => '1791382935001',
            'contactEmail' => 'administracion@fecabe.com.ec',
            'phone' => '022250883',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Amazonas y República, Edificio Las Cámaras, piso 9',
        ]);
        Company::create([
            'name' => 'FERPACORP',
            'idNumber' => '1722115696001',
            'contactEmail' => 'Bryan.ortiz@protonmail.com',
            'phone' => '02265628302',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Asunción y Buenos Aires',
        ]);
        Company::create([
            'name' => 'Fondo Ambiental',
            'idNumber' => '1768129900001',
            'contactEmail' => 'fambiental@quito.gob.ec',
            'phone' => '022430061',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Rio Coca E6-85 e Isla Genovesa',
        ]);
        Company::create([
            'name' => 'Forma Industria de Muebles Formadel Cia. Ltda.',
            'idNumber' => '1790979229001',
            'contactEmail' => 'gerencia@mueblesforma.com.ec',
            'phone' => '245-8001 / 5143486',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Galo Plaza Lasso N74-296 y Av. Juan de Selis',
        ]);
        Company::create([
            'name' => 'FV Área Andina S.A.',
            'idNumber' => '1790208087001',
            'contactEmail' => 'jespinosa@fvecuador.com',
            'phone' => '022991000',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Sangolqui, Vía Amaguaña Km.25',
        ]);
        Company::create([
            'name' => 'GARTEC',
            'idNumber' => '1707091102001',
            'contactEmail' => 'facturacion@gartec.com.ec',
            'phone' => '333-2972',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Shyris N35-71 Y Suecia',
        ]);
        Company::create([
            'name' => 'Georgina Luna',
            'idNumber' => '1708142508',
            'contactEmail' => 'info@georginaluna.com',
            'phone' => '0999652326',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Diego de Utreras N27-82 y Selva Alegre',
        ]);
        Company::create([
            'name' => 'HUGO TAMAYO TAPIA',
            'idNumber' => '1713267068001',
            'contactEmail' => 'htamayo@tamarcons.com',
            'phone' => '0997021695',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. República E7-61 y Martin Carrion',
        ]);
        Company::create([
            'name' => 'INACORPSA DEL ECUADOR S.A.',
            'idNumber' => '1791351177001',
            'contactEmail' => 'informacion@inacorpsa.com',
            'phone' => '022904129',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Juan Severino E6-80 Y Eloy Alfaro',
        ]);
        Company::create([
            'name' => 'Joe Vandino - ProInspectors',
            'idNumber' => 'N/A',
            'contactEmail' => 'info@proinspectorsfl.com',
            'phone' => 'N/A',
            'state' => 'Florida',
            'city' => 'N/A',
            'address' => 'United States',
        ]);
        Company::create([
            'name' => 'Karina Pavlova Valladares Nájera',
            'idNumber' => '1712509957',
            'contactEmail' => 'kpavna74@gmail.com',
            'phone' => '0960686005',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Yanacona N74 y Oe8',
        ]);
        Company::create([
            'name' => 'Laboratorio Clínico Analítica Biomédica',
            'idNumber' => '1792628822001',
            'contactEmail' => 'contabilidad@analiticabiomedica.com',
            'phone' => '02225 4565',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. 6 de Diciembre N34-143 e Irlanda',
        ]);
        Company::create([
            'name' => 'LABORATORIOS SIEGFRIED S.A.',
            'idNumber' => '1791897498001',
            'contactEmail' => 'facturacion_electronica@siegfried.com.ec',
            'phone' => '0224009600',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. República del Salvador N34-493 y Av. Portugal. Edificio Torre Gibraltar, planta baja.',
        ]);
        Company::create([
            'name' => 'LABSOPHIA DE ECUADOR CIA. LTDA.',
            'idNumber' => '1792623952001',
            'contactEmail' => 'juan.salazar@sophiaint.com',
            'phone' => '0989070661',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Shyris, N34-152, y Holanda. Edificio Shyris Center, piso 13, oficina #1301.',
        ]);
        Company::create([
            'name' => 'María Sylvana Ubidia Marín',
            'idNumber' => '1705893780001',
            'contactEmail' => 'sylvanaubidia@gmail.com',
            'phone' => '022889322',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Urb. Rincon del Valle Casa N 31',
        ]);
        Company::create([
            'name' => 'Megalabs-Pharma S.A.',
            'idNumber' => '1790822028001',
            'contactEmail' => 'sfuertes@megalabs.com.ec',
            'phone' => '022234661',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. Coruña N27-36 y Av. Orellana. Edif. La Moraleja, piso 9 y 10.',
        ]);
        Company::create([
            'name' => 'OHM & CO. CIA. LTDA. AUDITORES Y CONSULTORES',
            'idNumber' => '0992753803001',
            'contactEmail' => 'jguerrero@ohmecuador.com',
            'phone' => '043728370',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'PUERTO SANTA ANA, CIUDAD DEL RIO, EDIFICIO THE POINT',
        ]);
        Company::create([
            'name' => 'ONLINEBERATUNG CIA. LTDA.',
            'idNumber' => '1792925835001',
            'contactEmail' => 'contabilidad@onlineberatung.com.ec',
            'phone' => '02-382-6770',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'AV NACIONES UNIDAS TGM-6 NUÑEZ DE VELA OF 610 IÑAQUITO',
        ]);
        Company::create([
            'name' => 'PABLO SEBASTÍAN VIZCAÍNO MOREIRA',
            'idNumber' => '0503045411',
            'contactEmail' => 'sebastian_230489@hotmail.com',
            'phone' => '0987522726',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'El Inca Ánonas y Jazmines',
        ]);
        Company::create([
            'name' => 'PPROVO',
            'idNumber' => '1793119603001',
            'contactEmail' => 'luisa@pprovo.com',
            'phone' => '0999463962',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Bernardo de Legarda y pasaje F Cumbaya',
        ]);
        Company::create([
            'name' => 'Procesadora Vymsa',
            'idNumber' => '1792097215001',
            'contactEmail' => 'financiero@vymsa.net',
            'phone' => '022474090',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'José larrea y Francisco García',
        ]);
        Company::create([
            'name' => 'PRUEBAS SERVICIO DE RENTAS INTERNAS',
            'idNumber' => '1725656563001',
            'contactEmail' => 'rarmas@umpacto.com',
            'phone' => '0983508539',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Batán Alto',
        ]);
        Company::create([
            'name' => 'REPUBLICA DEL CACAO CACAOREPUBLIC CIA. LTDA.',
            'idNumber' => '1792363373001',
            'contactEmail' => 'facturacion@republicadelcacao.pro',
            'phone' => '0983178052',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Km 9.1/2 Panamericana Sur S35-60&nbsp;y&nbsp;Cóndor&nbsp;Ñan',
        ]);
        Company::create([
            'name' => 'Roentgen S.A.',
            'idNumber' => '0993386628001',
            'contactEmail' => 'alxhugom@gmail.com',
            'phone' => '0987888715',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Shirys y Emilio Zola',
        ]);
        Company::create([
            'name' => 'SAGCHA S.A.S.',
            'idNumber' => '1793211030001',
            'contactEmail' => 'info@sagcha.com',
            'phone' => '0992035046',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Calle Gonnessiat 157 y González Suárez',
        ]);
        Company::create([
            'name' => 'TAMARCONS CIA LTDA',
            'idNumber' => '1792094011001',
            'contactEmail' => 'administrativo@tamarcons.com',
            'phone' => '0997021695',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. República E7-61 y Martín Carrión',
        ]);
        Company::create([
            'name' => 'Tecknologistic S.A.',
            'idNumber' => '1792031869001',
            'contactEmail' => 'contabilidad@tecknologistic.com',
            'phone' => '022438236',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'De las Buganvillas N45-311 Y Av. Eloy Alfaro',
        ]);
        Company::create([
            'name' => 'TECTOCARBON S.A.',
            'idNumber' => '0993213101001',
            'contactEmail' => 'contabilidad@tectocarbon.com',
            'phone' => '593 42834566 ext. 103-106',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Km. 1.5 Via a Samborondón.  Edif. SBC Office Center, Piso 3, Oficina 34.',
        ]);
        Company::create([
            'name' => 'Viajes y Destinos Destv',
            'idNumber' => '1792467764001',
            'contactEmail' => 'gerencia@viajesydestinos.ec',
            'phone' => '022541022',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Av. 6 de Diciembre y La Niña. Edificio Multicentro. piso 10. Oficina 1005',
        ]);
        Company::create([
            'name' => 'Whiskys Cia Ltda',
            'idNumber' => '1792765978001',
            'contactEmail' => 'facturacion@whiskys.com.ec',
            'phone' => '0980302106',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Alfonso Lamiña S/N',
        ]);
        Company::create([
            'name' => 'Ximena Burbano Rivera',
            'idNumber' => '1709979726001',
            'contactEmail' => 'ximeburbano@gmail.com',
            'phone' => '0994030776',
            'state' => 'Pichincha',
            'city' => 'Quito',
            'address' => 'Robles y Amazonas',
        ]);


    }
}