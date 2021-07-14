# OctoberCMS-pluginDBStorageLite
DB Storage Lite proporciona un adaptador para almacenar contenido de October CMS en una base de datos.

Este plugin se encuentra deprecado si se instala directamante desde la sección de Plugins del backend de October CMS. 
Al momento de realizar la instalación del plugin les van a aparecer varios errores, esto se debe a que no le dieron más soporte. 
En este repositorio se deja disponible una solución en donde se corrigen los errores que el plugin presenta por su discontinuidad, dejando el plugin totalmente funcional. 
Lo que hace este plugin es guardar el contenido generado desde October CMS directamente en la base de datos que tengamos definida. 

Ambiente de trabajo:
En mi caso lo que hice fue levantar dos máquinas virtuales con sistema operativo Centos 7.7. En una de las máquinas virtuales instalar October CMS y en la otra virtual instalar Mysql 8.0.25 como motor base de datos. Previa a la instalación de October CMS, se debe instalar Php versión 7.3.29 en donde se vaya a instalar el sistema October CMS. 
Debemos tener en cuenta tema de permisos/firewalls para no tener problemas de comunicación entre nuestras máquinas virtuales. 

Les aconsejo seguir la guía de instalación de October CMS presente en este link: https://hostpresto.com/community/tutorials/install-october-cms-on-centos-7/
Para la instalación de Php, pueden guiarse por el siguiente link: https://comoinstalar.me/como-instalar-php-en-centos-7/

Antes de instalar el plugin, deben tener configurada la base de datos. Una vez instalado el plugin, crea una tabla dentro de nuestra base de datos en donde va a ir guardado el contenido generado en October CMS. Debemos ir a la sección Settings --> Storage Settings y ahi nos aparecera una opción que debe quedar en ON (Use DB storage Turn this option on if you want your content into DB). Aqui mismo podemos configurar lo que queremos que el plugin guarde en nuestra base de datos (Layouts, Page, Partials, Content).
Una vez realizada esta configuración, guardamos los cambios. Las futuras páginas que creemos desde October CMS comenzaran a guardarse en la base de datos definida. 

Espero les resulte útil. 

Saludos :)

