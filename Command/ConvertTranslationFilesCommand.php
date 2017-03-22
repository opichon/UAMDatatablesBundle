<?php

namespace UAM\Bundle\DatatablesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;

class ConvertTranslationFilesCommand extends ContainerAwareCommand
{
    protected $languages;

    protected function configure()
    {
        $this
            ->setName('uam:datatables:i18n')
            ->setDescription('Convert translation files to new format');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from_dir = dir(__DIR__.'/../Resources/config/bower_components/datatables-plugins/i18n');

        $to_dir = dir(__DIR__.'/../Resources/public/vendor/datatables-plugins/intl');

        while (false !== ($entry = $from_dir->read())) {
            if ('.' == $entry || '..' == $entry) {
                continue;
            }

            $filename = $this->getNewFilename($entry);

            $output->writeln(sprintf('Processing %s to %s', $entry, $filename));

            $path = sprintf('%s/%s', $from_dir->path, $entry);

            if (!is_file($path)) {
                $output->writeln(sprintf('No file found at %s', $path));
            }

            $contents = file_get_contents($path);

            $contents = substr($contents, strpos($contents, '{'));

            $contents = json_decode($contents, true);

            if (null === $contents) {
                $output->writeln(sprintf(
                    'Could not parse contents of %s',
                    $entry
                ));

                continue;
            }

            if (!array_key_exists('processing', $contents)) {
                $lang = array();

                foreach ($contents as $key => $trans) {
                    if ($i18n = $this->map($key, $trans)) {
                        $lang = array_merge($lang, $i18n);
                    }
                }
                $contents = json_encode($lang, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $contents = json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

//            $output->writeln($contents);

            file_put_contents(
                sprintf('%s/%s', $to_dir->path, $filename),
                $contents
            );
        }
    }

    protected function getNewFilename($filename)
    {
        $lang = substr($filename, 0, -5);

        return sprintf('%s.json', $this->getLanguageMappings()[$lang]);
    }

    protected function getLanguages()
    {
        if (!$this->languages) {
            \Locale::setDefault('en');

            $this->languages = array_flip(Intl::getLanguageBundle()->getLanguageNames());
        }

        return $this->languages;
    }

    protected function map($key, $trans)
    {
        if (in_array($key, $this->getIgnoredKeys())) {
            return;
        }

        $mapped = $this->getKeyMappings()[$key];

        if (is_array($trans)) {
            $data = array();

            foreach ($trans as $k => $t) {
                $i18n = $this->map($k, $t);

                if ($i18n) {
                    $data = array_merge($data, $i18n);
                }
            }

            return array($mapped => $data);
        } else {
            return array($mapped => $trans);
        }
    }

    protected function getKeyMappings()
    {
        return array(
            'sEmptyTable' => 'emptyTable',
            'sInfo' => 'info',
            'sInfoEmpty' => 'infoEmpty',
            'sInfoFiltered' => 'infoFiltered',
            'sInfoPostFix' => 'infoPostFix',
            'sInfoThousands' => 'thousands',
            'sLengthMenu' => 'lengthMenu',
            'sLoadingRecords' => 'loadingRecords',
            'sProcessing' => 'processing',
            'sSearch' => 'search',
            'sThousands' => 'thousands',
            'sZeroRecords' => 'zeroRecords',
            'oPaginate' => 'paginate',
            'sFirst' => 'first',
            'sPrevious' => 'previous',
            'sNext' => 'next',
            'sLast' => 'last',
            'oAria' => 'aria',
            'sSortAscending' => 'sortAscending',
            'sSortDescending' => 'sortDescending',
        );
    }

    protected function getIgnoredKeys()
    {
        return array(
            'sUrl',
        );
    }

    protected function getLanguageMappings()
    {
        return array(
            'Afrikaans' => 'af',
            'Albanian' => 'sq',
            'Arabic' => 'ar',
            'Armenian' => 'hy',
            'Azerbaijan' => 'az',
            'Bangla' => 'bn',
            'Basque' => 'eu',
            'Belarusian' => 'be',
            'Bulgarian' => 'bg',
            'Catalan' => 'ca',
            'Chinese-traditional' => 'zh-Hant',
            'Chinese' => 'zh',
            'Croatian' => 'hr',
            'Czech' => 'cs',
            'Danish' => 'da',
            'Dutch' => 'nl',
            'English' => 'en',
            'Estonian' => 'et',
            'Filipino' => 'tg',
            'Finnish' => 'fi',
            'French' => 'fr',
            'Galician' => 'gl',
            'Georgian' => 'ka',
            'German' => 'de',
            'Greek' => 'el',
            'Gujarati' => 'gu',
            'Hebrew' => 'he',
            'Hindi' => 'hi',
            'Hungarian' => 'hu',
            'Icelandic' => 'is',
            'Indonesian' => 'id',
            'Indonesian-Alternative' => 'id-alt',
            'Irish' => 'ga',
            'Italian' => 'it',
            'Japanese' => 'ja',
            'Korean' => 'ko',
            'Kyrgyz' => 'ky',
            'Latvian' => 'lv',
            'Lithuanian' => 'lt',
            'Macedonian' => 'mk',
            'Malay' => 'ms',
            'Mongolian' => 'mn',
            'Nepali' => 'ne',
            'Norwegian' => 'no',
            'Persian' => 'fa',
            'Polish' => 'pl',
            'Portuguese-Brasil' => 'pt-BR',
            'Portuguese' => 'pt',
            'Romanian' => 'ro',
            'Russian' => 'ru',
            'Serbian' => 'sr',
            'Sinhala' => 'si',
            'Slovak' => 'sk',
            'Slovenian' => 'sl',
            'Spanish' => 'es',
            'Swahili' => 'sw',
            'Swedish' => 'sv',
            'Tamil' => 'ta',
            'Thai' => 'th',
            'Turkish' => 'tr',
            'Ukranian' => 'uk',
            'Urdu' => 'ur',
            'Uzbek' => 'uz',
            'Vietnamese' => 'vi',
        );
    }
}
