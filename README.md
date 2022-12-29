# NotedAPI Interview

API desenvolvida com Framework CodeIgniter 3 e PHP na versão 7.3.29
*    Essa API foi desenvolvida para o projeto [NotedApp](https://github.com/claudio-henriq/NotedAPP)

Para executar esse projeto acesse a pasta raiz do projeto (ex. **NotedAPI**) e execute o comando PHP:

```bash
$ php -S 192.168.1.107:8000
```

Substitua onde se encontra **192.168.1.107:8000** pelo `IP local da maquina` e por uma `porta disponível`.

O projeto funciona também com servers criados no **XAMP** e similares.

Para configurar a base de dados corretamente acesse o arquivo Database localizado em **\NotedAPI\application\config\database.php** e altere as segintes linhas

Onde se encontra
>	`'hostname' => 'localhost'`   --->> Altere `localhost` para o endereço da base de dados

>   `'username' => 'root'`        --->> Altere `root` para o seu usuário configurado na sua base de dados

>   `'password' => ''`            --->> Altere `''` para a sua senha configurada na sua base de dados

Com essas configurações já é possivel utilizar a API.

O endereço de acesso configurado anteriormente que será utilizado pelo APP
