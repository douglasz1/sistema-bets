# SOBRE ESSE PROJETO

Introdução de como iniciar o ambiente de desenvolvimento do sistema.

## PARA INICIALIZAR ESSE PROJETO

- Clonar esse repositório na máquina;
- Execute os containers do docker `docker-compose up -d`;
- Entre no container da aplicação `docker-compose exec app bash`;
- Rodar na raíz o comando `composer create-project`;
- Cria o link simbólico da pasta storage `php artisan storage:link`;
- Crie a pasta para conter as bandeiras `mkdir public/storage/flags`;
- Rodar as migrations utilizando o comando `php artisan migrate --seed`.

### Atulizando eventos

`php artisan partidas:atualizar`
