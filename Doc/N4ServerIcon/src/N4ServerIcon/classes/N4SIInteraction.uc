/*
    Filename: N4SIInteraction.uc
    Author: Neil Popplewell
    Email: neo@koalaclaw.com

   Copyright © 2004 Neil Popplewell.  All rights reserved.
*/
class N4SIInteraction extends Interaction;

function float GetCurrentTime()
{
  if(ViewPortOwner == none || ViewportOwner.Actor == none || ViewportOwner.Actor.Level == none) return 0.0;
  return ViewportOwner.Actor.Level.TimeSeconds;
}
function SetFocus()
{
  if(Master == none) return;
  Master.SetFocusTo(self,ViewportOwner);
}
function R6PlayerController GetR6PC()
{
  if(ViewportOwner == none) return none;
  return R6PlayerController(ViewportOwner.Actor);
}
function R6Pawn GetR6Pawn()
{
  local R6PlayerController pc;
  pc=GetR6PC();
  if(pc == none) return none;
  return pc.m_pawn;
}
function bool IsAlive()
{
  local R6Pawn pawn;
  pawn = getR6Pawn();
  if(pawn == none) return false;
  return pawn.isAlive();
}

function RemoveSelf()
{
  local InteractionMaster tMaster;
  SetRemoved();
  tMaster = Master;
  if(ViewportOwner != none)
  {
    if(tMaster == none)
    {
      tMaster=ViewportOwner.InteractionMaster;
    }
  }
  if(tMaster != none)
  {
     tMaster.RemoveInteraction(self);
  }
  else
  {
    Log(self@"No master interaction to remove self from");
  }
  SetRemoved();
  ViewportOwner=none;
  Master=none;
  ClearOuter();
  bVisible=false;
  bActive=false;
}
function SetRemoved()
{
}
defaultproperties
{
}
