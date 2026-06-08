<?php

namespace Core\Library;

use Core\Library\Session;

class Files
{
    private string $pathFile;
    private array $allowedTypes;
    private int $maxSize;

    public function __construct(
        string $uploadPath = '..' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR,
        array $allowedTypes = FILE_ALLOWEDTYPES,
        int $maxSizeMB = FILE_MAXSIZE
    ) {
        $this->pathFile = $uploadPath;
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSizeMB * 1024 * 1024;
    }

    /**
     * @return array Nomes dos arquivos enviados (vazio se todos falharam); false em erro fatal de diretório.
     */
    public function upload(array $arquivos, string $pasta, string $nomeArquivoAntigo = ''): array|false
    {
        $diretorioUpload = $this->pathFile . $pasta . DIRECTORY_SEPARATOR;
        $arquivosUploadSucesso = [];
        $erros = [];

        if (!is_dir($diretorioUpload) && !mkdir($diretorioUpload, 0777, true)) {
            Session::set('msgError', "Falha ao criar o diretório de upload: {$diretorioUpload}");
            return false;
        }

        if (!is_writable($diretorioUpload)) {
            Session::set('msgError', "O diretório de upload não tem permissões de escrita: {$diretorioUpload}");
            return false;
        }

        foreach ($arquivos as $arquivo) {
            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $erros[] = "{$arquivo['name']}: " . $this->uploadErrorMsg($arquivo['error']);
                continue;
            }

            if (!empty($this->allowedTypes)) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeReal = $finfo->file($arquivo['tmp_name']);
                if (!in_array($mimeReal, $this->allowedTypes)) {
                    $erros[] = "Tipo de arquivo inválido: {$arquivo['name']}";
                    continue;
                }
            }

            if ($arquivo['size'] > $this->maxSize) {
                $erros[] = "Tamanho do arquivo excedido: {$arquivo['name']}";
                continue;
            }

            $extensao  = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $nomeBase  = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($arquivo['name'], PATHINFO_FILENAME));
            $nomeArquivo   = bin2hex(random_bytes(8)) . '_' . $nomeBase . '.' . $extensao;
            $caminhoCompleto = $diretorioUpload . $nomeArquivo;

            if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                $erros[] = "Falha ao mover o arquivo: {$arquivo['name']}";
                continue;
            }

            // Arquivo antigo só é removido após o novo upload ser confirmado
            if (!empty($nomeArquivoAntigo)) {
                $caminhoArquivoAntigo = $diretorioUpload . $nomeArquivoAntigo;
                if (file_exists($caminhoArquivoAntigo)) {
                    unlink($caminhoArquivoAntigo);
                }
            }

            $arquivosUploadSucesso[] = $nomeArquivo;
        }

        if (!empty($erros)) {
            Session::set('msgError', implode('<br>', $erros));
        }

        return $arquivosUploadSucesso;
    }

    public function delete(string $nomeArquivo, string $pasta): bool
    {
        $caminhoCompleto = $this->pathFile . $pasta . DIRECTORY_SEPARATOR . $nomeArquivo;
        return file_exists($caminhoCompleto) && unlink($caminhoCompleto);
    }

    /**
     * Retorna o caminho absoluto de um arquivo armazenado.
     */
    public function getPath(string $nomeArquivo, string $pasta): string
    {
        return $this->pathFile . $pasta . DIRECTORY_SEPARATOR . $nomeArquivo;
    }

    /**
     * Verifica se um arquivo armazenado existe no disco.
     */
    public function exists(string $nomeArquivo, string $pasta): bool
    {
        return file_exists($this->getPath($nomeArquivo, $pasta));
    }

    /**
     * Retorna o tamanho em bytes de um arquivo armazenado, ou false se não existir.
     */
    public function getSize(string $nomeArquivo, string $pasta): int|false
    {
        $caminho = $this->getPath($nomeArquivo, $pasta);
        return file_exists($caminho) ? filesize($caminho) : false;
    }

    /**
     * Retorna o MIME type real de um arquivo armazenado, ou false se não existir.
     */
    public function getMimeType(string $nomeArquivo, string $pasta): string|false
    {
        $caminho = $this->getPath($nomeArquivo, $pasta);

        if (!file_exists($caminho)) {
            return false;
        }

        return (new \finfo(FILEINFO_MIME_TYPE))->file($caminho);
    }

    /**
     * Remove o prefixo hex aleatório gerado no upload e devolve o nome original.
     * Exemplo: "a1b2c3d4e5f60708_relatorio.pdf" → "relatorio.pdf"
     */
    public static function getNomeOriginal(string $nomeArquivo): string
    {
        return preg_replace('/^[0-9a-f]{16}_/', '', $nomeArquivo);
    }

    private static function uploadErrorMsg(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'o arquivo excede o tamanho máximo permitido pelo servidor (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE  => 'o arquivo excede o tamanho máximo definido no formulário.',
            UPLOAD_ERR_PARTIAL    => 'o arquivo foi enviado apenas parcialmente. Tente novamente.',
            UPLOAD_ERR_NO_FILE    => 'nenhum arquivo foi enviado.',
            UPLOAD_ERR_NO_TMP_DIR => 'pasta temporária do servidor não encontrada. Contate o suporte.',
            UPLOAD_ERR_CANT_WRITE => 'falha ao gravar o arquivo no disco. Contate o suporte.',
            UPLOAD_ERR_EXTENSION  => 'upload bloqueado por extensão PHP do servidor.',
            default               => "erro desconhecido (código {$code}).",
        };
    }

    /**
     * Converte bytes em representação legível (B, KB, MB, GB).
     */
    public static function formatBytes(int $bytes, int $precisao = 2): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($unidades) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $precisao) . ' ' . $unidades[$i];
    }

    /**
     * Normaliza o array $_FILES de múltiplos arquivos (name[], tmp_name[], …)
     * para um array indexado de arquivos individuais, compatível com upload().
     */
    public static function normalizeFiles(array $files): array
    {
        $normalized = [];
        $count      = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            $normalized[] = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
        }

        return $normalized;
    }

    /**
     * Serve um arquivo para o navegador com os headers adequados.
     * Imagens, SVG e PDF abrem inline; demais tipos disparam download.
     * Interrompe a execução após o envio.
     *
     * @param bool $forceDownload  Força modo attachment mesmo para tipos exibíveis inline.
     */
    public function serve(string $nomeArquivo, string $pasta, bool $forceDownload = false): void
    {
        $caminho = $this->getPath($nomeArquivo, $pasta);

        if (!file_exists($caminho)) {
            http_response_code(404);
            exit('Arquivo não encontrado no servidor.');
        }

        $mime         = $this->getMimeType($nomeArquivo, $pasta);
        $inlineTypes  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',
                         'image/bmp', 'image/svg+xml', 'application/pdf'];
        $disposicao   = (!$forceDownload && in_array($mime, $inlineTypes)) ? 'inline' : 'attachment';
        $nomeOriginal = self::getNomeOriginal($nomeArquivo);

        header('Content-Type: ' . $mime);
        header("Content-Disposition: {$disposicao}; filename=\"{$nomeOriginal}\"");
        header('Content-Length: ' . filesize($caminho));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($caminho);
        exit;
    }
}
