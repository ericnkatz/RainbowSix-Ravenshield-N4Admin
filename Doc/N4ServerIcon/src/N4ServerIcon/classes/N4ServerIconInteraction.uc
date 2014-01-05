/*
    Filename: N4ServerIconInteraction.uc
    Author: Neil Popplewell
    Email: neo@koalaclaw.com

   Copyright © 2004 Neil Popplewell.  All rights reserved.
*/
class N4ServerIconInteraction extends N4SafeInteraction;

enum N4FadeInfo
{
  FADE_None,
  FADE_In,
  Fade_Out
};
var N4ServerIconLoader.N4IconInfo Icon[4];
var Color DefaultColor;
var bool bTakeFocus;
var bool bPawnAlive;
var float LastDeltaTime;
var float FrameDeltaTime;
var float LastFrameTime;

event Initialized()
{
  super.Initialized();
  LastFrameTime=GetCurrentTime();
}
function bool isColorNull(Color c)
{
  return c.R == 0 && c.G == 0 && c.B == 0 && c.A==0;
}
function InitializeIcon(N4ServerIconLoader.N4IconInfo iInfo[4], optional bool _bTakeFocus)
{
  local int i;
  bTakeFocus=_bTakeFocus;
  for(i=0;i<ArrayCount(Icon);i++)
  {
    Icon[i]=iInfo[i];
    if(isColorNull(Icon[i].color))
    {
      Log(self@"using default color");
      Icon[i].color=DefaultColor;
    }
  }

  if(WasGlobal && ViewportOwner != none) //For global interactions
  {
    RelevantPC=ViewportOwner.Actor;
  }
  if(bTakeFocus)
  {
    SetFocus();
  }
  LastFrameTime=GetCurrentTime();
}
function UpdateFrameDeltaTime()
{
/*  local float CurrentTime;
  CurrentTime=GetCurrentTime();
  FrameDeltaTime=LastFrameTime-CurrentTime;
  LastFrameTime=CurrentTime;                            */
  FrameDeltaTime=LastDeltaTime;
 // FrameDeltaTime=FClamp(FrameDeltaTime,0.0,3.0);
}
function PostRender(Canvas C)
{
  local color OldDrawColor;
  super.PostRender(C);
  UpdateFrameDeltaTime();
  OldDrawColor=C.DrawColor;
  C.Reset();
  DrawAllIcons(C);
  C.Reset();
  C.DrawColor=OldDrawColor;
}
function DrawAllIcons(Canvas C)
{
  local int i;
  C.UseVirtualSize(true,640,480);
  for(i=0;i<ArrayCount(Icon);i++)
  {
    DrawIcon(C,Icon[i],FrameDeltaTime,bPawnAlive);
  }
  C.UseVirtualSize(false);
}
event Tick(float t)
{
  super.Tick(t);
  LastDeltaTime=t;
  bPawnAlive=IsAlive(); //Do this here to save some processor power
}

static function DrawIcon(Canvas C, out N4ServerIconLoader.N4IconInfo icon, float DeltaTime, optional bool bPawnIsAlive)
{
  local Color oldColor,drawColor;
  local N4ServerIconLoader.N4Point oldPos;
  local float CurrentAlpha,DesiredAlpha;
  local N4FadeInfo Fade;
  if(icon.icon == none) return;
  DesiredAlpha=float(icon.color.a);
  CurrentAlpha=Icon.CurrentAlpha;
  if(bPawnIsAlive)
  {
    if(icon.bShowWhileAlive)
    {
      fade=FADE_In;
    }
    else
    {
      fade=FADE_Out;
    }
  }
  else
  {
    if(icon.bDoNotShowWhileDead)
    {
      Fade=FADE_Out;
    }
    else
    {
      Fade=FADE_In;
    }
  }

  switch(Fade)
  {
    case FADE_In:
      icon.CurrentStartUpTime+=DeltaTime;
      if(icon.StartUpTime <= 0.0)
      {
        icon.CurrentStartUpTime=0.0;
      }
      else if(icon.CurrentStartUpTime >= icon.StartUpTime)
      {
        icon.CurrentStartUpTime=icon.StartUpTime;
      }
      else
      {
        if(icon.CurrentStartUpTime < 0.0) icon.CurrentStartUpTime=0.0;
        return;
      }
      if(icon.FadeInTime <= 0)
      {
        CurrentAlpha=DesiredAlpha;
      }
      else
      {
        CurrentAlpha+=DesiredAlpha*(DeltaTime/icon.FadeInTime);
      }
      break;
    case FADE_Out:
      icon.CurrentStartUpTime=0.0;
      if(icon.FadeOutTime <= 0)
      {
        CurrentAlpha=0.0;
      }
      else
      {
        CurrentAlpha-=DesiredAlpha*(DeltaTime/icon.FadeOutTime);
      }
      break;
    default:
      CurrentAlpha=DesiredAlpha;
  }
  CurrentAlpha=FClamp(CurrentAlpha,0.0,DesiredAlpha);
  icon.CurrentAlpha=CurrentAlpha;
  drawColor=icon.color;
  drawColor.A=byte(CurrentAlpha);
  if(drawColor.A == 0)
  {
    return;
  }
  oldPos.x=C.CurX;
  oldPos.y=C.CurY;
  oldColor=C.DrawColor;
  C.DrawColor=drawColor;
  C.SetPos(icon.pos.x,icon.pos.y);
  C.DrawTile(icon.icon,icon.DrawSize.x,icon.DrawSize.y,icon.startpos.x
             ,icon.startpos.y,icon.size.x,icon.size.y,icon.RotationAngle);
  c.DrawColor=oldColor;
  c.SetPos(oldPos.x,oldPos.y);
}

defaultproperties
{
  bVisible=true
  DefaultColor=(R=255,G=255,B=255,A=127)
}
