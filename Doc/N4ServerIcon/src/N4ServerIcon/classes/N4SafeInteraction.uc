/*
    Filename: N4SafeInteraction.uc
    Author: Neil Popplewell
    Email: neo@koalaclaw.com

   Copyright © 2004 Neil Popplewell.  All rights reserved.
*/
class N4SafeInteraction extends N4SIInteraction;
var protected PlayerController RelevantPC;
var protected bool WasGlobal;
event Initialized()
{
  super.Initialized();
  if(ViewportOwner != none)
    RelevantPC=ViewportOwner.Actor;
  else WasGlobal=true;
}

function SetFocus()
{
  if(Master == none) return;
  if(WasGlobal)
      Master.SetFocusTo(self,none);
  else Master.SetFocusTo(self,ViewportOwner);
}
function bool ShouldRemove()
{
  return  (ViewPortOwner != none && (ViewportOwner.Actor != RelevantPC));
}

function RemoveSelf()
{
  if(WasGlobal)
  {
    ViewportOwner=none;
  }
  super.RemoveSelf();
}
function Tick(float DeltaTime)
{
  super.Tick(DeltaTime);
  if(ShouldRemove())
  {
    RemoveSelf();
  }
}
defaultproperties
{
  bRequiresTick=true
}
