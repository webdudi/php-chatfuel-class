<?php

namespace filipmihal;


class ChatFuel
{
  const VERSION = '1.0.0';

  public function __construct($debug = FALSE)
  {
    if (( ! $debug) && ( ! isset($_SERVER['HTTP_USER_AGENT']) OR strpos($_SERVER['HTTP_USER_AGENT'], 'Apache-HttpAsyncClient') === FALSE)) {
      exit;
    }
  }

  public function getResult($messages)
  {
        return array('messages' => $messages);
  }


  public function getText($messages = null)
  {
    if (is_null($messages)) {
      throw new Exception('Invalid input', 1);
    }

    $type = gettype($messages);
    if ($type === 'string') {
      return array('text' => $messages);
    } elseif ($type === 'array' || is_array($messages)) {
      foreach ($messages as $message) {
        return array('text' => $message);
      }
    } else {
      return array('text' => 'Error!');
    }
  }

  public function getImage($url)
  {
    if ($this->isURL($url)) {
      return $this->getAttachment('image', array('url' => $url));
    } else {
     return $this->getText('Error: Invalid URL!');
    }
  }

  public function getVideo($url)
  {
    if ($this->isURL($url)) {
     return $this->getAttachment('video', array('url' => $url));
    } else {
      return $this->getText('Error: Invalid URL!');
    }
  }

  public function getAudio($url)
  {
    if ($this->isURL($url)) {
      return $this->getAttachment('audio', array('url' => $url));
    } else {
      return $this->getText('Error: Invalid URL!');
    }
  }

  public function getTextCard($text, $buttons)
  {
    if (is_array($buttons)) {
      return $this->getAttachment('template', array(
        'template_type' => 'button',
        'text'          => $text,
        'buttons'       => $buttons
      ));
    }

    return FALSE;
  }

  public function getGallery($elements)
  {
    if (is_array($elements)) {
      return $this->getAttachment('template', array(
        'template_type' => 'generic',
        'elements'      => $elements
      ));
    }

    return FALSE;
  }

  public function createElement($title, $image, $subTitle, $buttons)
  {
    if (is_array($buttons)) {
      return array(
        'title'     => $title,
        'image_url' => $image,
        'subtitle'  => $subTitle,
        'buttons'   => $buttons
      );
    }

    return FALSE;
  }

  public function createButtonToBlock($title, $block, $setAttributes = NULL)
  {
    $button = array();
    $button['type'] = 'show_block';
    $button['title'] = $title;
    
    if (is_array($block)) {
      $button['block_names'] = $block;
    } else {
      $button['block_name'] = $block;
    }

    if ( ! is_null($setAttributes) && is_array($setAttributes)) {
      $button['set_attributes'] = $setAttributes;
    }

    return $button;
  }

  public function createButtonToURL($title, $url, $setAttributes = NULL)
  {
      $button = array();
      $button['type'] = 'web_url';
      $button['url'] = $url;
      $button['title'] = $title;
      
      if ( ! is_null($setAttributes) && is_array($setAttributes)) {
        $button['set_attributes'] = $setAttributes;
      }

      return $button;
    
  }

  public function createPostBackButton($title, $url)
  {
    if ($this->isURL($url)) {
      return array(
        'url'   => $url,
        'type'  => 'json_plugin_url',
        'title' => $title
      );
    }

    return FALSE;
  }

  public function createCallButton($phoneNumber, $title = 'Call')
  {
    return array(
      'type'         => 'phone_number',
      'phone_number' => $phoneNumber,
      'title'        => $title
    );
  }

  public function createShareButton()
  {
    return array('type' => 'element_share');
  }

  public function createQuickReply($text, $quickReplies)
  {
    if (is_array($quickReplies)) {
      return array('text' => $text, 'quick_replies' => $quickReplies);
    }

    return FALSE;
  }

  public function createQuickReplyButton($title, $block)
  {
    $button = array();
    $button['title'] = $title;

    if (is_array($block)) {
      $button['block_names'] = $block;
    } else {
      $button['block_name'] = $block;
    }

    return $button;
  }

  private function getAttachment($type, $payload)
  {
    $type = strtolower($type);
    $validTypes = array('image', 'video', 'audio', 'template');

    if (in_array($type, $validTypes)) {
      return array(
        'attachment' => array(
          'type'    => $type,
          'payload' => $payload
        )
      );
    } else {
      return array('text' => 'Error: Invalid type!');
    }
  }

  private function isURL($url)
  {
    return filter_var($url, FILTER_VALIDATE_URL);
  }
}
