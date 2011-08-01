<?php
/**
* EmailHelper Nodule Class
* 
* Created by NOLOH LLC.
* This class can be used to send Email either through an instance, or directly
* through the static Email function. If you have a RichMessage (HTML), and no plain text
* EmailHelper will always send out multi-part message for you automatically.
* 
* Emails should be formatted according to standard PHP rules:
* user@example.com
* User <user@example.com
* 
* Note that To, CC, and BCC are Arraylists, this means that you can do ->Add,or ->AddRange:
* <code>
* $email = new EmailHelper();
* $email->To->Add('john@abc.com');
* $email->To->AddRange('mary@def.com', 'james@abc.com');
* $email->To->AddRange(array('jake@ghi.com', 'sam@jkl.com'));
* </code>
* 
* @link http://www.noloh.com
*/
class ContentSlider extends Panel
{
	private $SlideHolder;
	private $Slides;
	private $PrevIndex;
	
	function ContentSlider($content=null, $left, $top, $width, $height)
	{
		parent::Panel($left, $top, $width, $height);
		WebPage::That()->CSSFiles->Add(GetRelativePath(getcwd(), dirname(__FILE__)) . '/Styles/default.css');
		$this->CSSClasses->Add('ContentSlider');
		$this->SlideHolder = new Panel(0, 0, '100%', '100%');
		$this->Slides = new Group();
		$this->Slides->Change = new ServerEvent($this, 'SlideContent');
		$this->SlideHolder->Controls->Add($this->Slides);
		if($content)
			$this->SetContent($content);
		$this->Controls->Add($this->SlideHolder);
		$this->SetPrevious();
		$this->SetNext();
	}
	function GetSlideHolder()	{return $this->SlideHolder;}
	function GetSlides()		{return $this->Slides;}
	function SetContent($content)
	{
		if(is_array($content))
		{
			foreach($content as $item)
			{
				$result = self::ContentHelper($item);
				if($result instanceof Control)
				{
					$this->Slides->Add($result);
					$result->Visible = false;
				}
			}
		}
		if($this->Slides->Count > 0)
			$this->Slides->SelectedIndex = 0;
		/*elseif(is_string($content))
		{
			elseif(is_file($content))	
		}*/
	}
	private static function ContentHelper($item)
	{
		if(is_array($item))
		{
			$item = array_change_key_case($item);
			if(isset($item['path']))
				$path = $item['path'];
			if(isset($item['url']))
				$url = $item['url'];
		}
		else
			$path = $item;
		$pathInfo = pathinfo($path);
		$extension = $pathInfo['extension'];
		if(preg_match('/jpeg|jpg|png|gif|bmp/', $extension))
		{
			$object = new Image($path);
//			$image->Select = new ClientEvent('')($image, 'SetSelected', true);
//			return new Image($string);
		}
		if(isset($url))
		{
			$object = new Link($url, $object);
			$object->Target = Link::NewWindow;
		}
		return $object;
	}
	function SetNext($object=null)
	{
		if(!$object)
		{
			//Temporary Defaults Until Designed
			$object = new Label('>', 0, 0, null, null);
		}
		if(isset($this->Controls['Next']))
			$this->Controls['Next']->Leave();
		if(!$object->CSSClasses->Contains('Next'))	
			$object->CSSClasses->Add('Next');
		$object->ReflectAxis('x');
		$this->Controls['Next'] = $object;
		$object->Click = new ServerEvent($this, 'MakeSelected');
		
	}
	function GetNext()	{return $this->Next;}
	function SetPrevious($object=null)
	{
		if(!$object)
		{
			//Temporary Defaults Until Designed
			$object = new Label('<', 0, 0, null, null);
		}
		if(isset($this->Controls['Previous']))
			$this->Controls['Previous']->Leave();
		if(!$object->CSSClasses->Contains('Previous'))	
			$object->CSSClasses->Add('Previous');
		$this->Controls['Previous'] = $object;
		$object->Click = new ServerEvent($this, 'MakeSelected', false);
	}
	function GetPrevious()	{return $this->Previous;}
	function MakeSelected($forward=true)
	{
		$count = $this->Slides->Count();
		if($count == 0)
			return;
		$selectedIndex = $this->Slides->SelectedIndex;
		$currentIndex = $forward? $selectedIndex + 1:$selectedIndex - 1;
		if($currentIndex >= 0 && $currentIndex < $count)
		{
			$this->Slides->SelectedIndex = $currentIndex;
		}
		else
			$this->Slides->SelectedIndex = 0;
	}
	function SlideContent()
	{
		$selectedPosition = $this->Slides->SelectedPosition;
		$animateLeft = $selectedPosition > $this->PrevIndex;
		if(isset($this->Slides[$this->PrevIndex]))
		{
			$slide = $this->Slides[$this->PrevIndex];
			$to = $animateLeft?(-1 * $slide->Width):($slide->Width);
			Animate::Left($slide, $to, 500, Animate::Quadratic, null, 45);
			if($slide->AnimationStop->Blank())
				$slide->AnimationStop = new ClientEvent('ToggleVisibility', $slide);
			$slide->AnimationStop->Enabled = true;
		}
		if($slide = $this->Slides->SelectedElement)
		{
			$slide->Visible = true;
			if($this->PrevIndex !== null)
			{
				$slide->Left = $animateLeft?$slide->Width:(-1 * $slide->Width);
				Animate::Left($slide, 0, 500, Animate::Quadratic, null, 45);
				$slide->AnimationStop->Enabled = false;
			}
			$this->PrevIndex = $this->Slides->SelectedIndex;
		}
	}
}
?>
