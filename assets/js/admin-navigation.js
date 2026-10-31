 
(() => {
 const sidebar=document.getElementById('admin-navigation');
 const toggle=document.querySelector('.admin-nav-toggle');
 const close=sidebar?.querySelector('.admin-nav-close');
 const overlay=document.querySelector('.admin-drawer-overlay');
 if(!sidebar||!toggle||!close||!overlay)return;
 const mobile=matchMedia('(max-width: 991px)');
 const content=document.querySelector('.admin-content'),user=document.querySelector('.navbar-user');
 let opened=false;
 function setOpen(value,restore=true){
  opened=value&&mobile.matches;
  document.body.classList.toggle('admin-nav-open',opened);
  toggle.setAttribute('aria-expanded',String(opened));
  toggle.setAttribute('aria-label',opened?'Close navigation':'Open navigation');
  overlay.hidden=!opened;sidebar.inert=mobile.matches&&!opened;
  if(content)content.inert=opened;if(user)user.inert=opened;
  if(opened){sidebar.setAttribute('role','dialog');sidebar.setAttribute('aria-modal','true');close.focus();}
  else{sidebar.removeAttribute('role');sidebar.removeAttribute('aria-modal');if(restore&&mobile.matches)toggle.focus();}
 }
 toggle.addEventListener('click',()=>setOpen(!opened));close.addEventListener('click',()=>setOpen(false));overlay.addEventListener('click',()=>setOpen(false));
 sidebar.addEventListener('click',event=>{if(event.target.closest('a[href]')&&opened)setOpen(false,false);});
 document.addEventListener('keydown',event=>{
  if(!opened)return;
  if(event.key==='Escape'){event.preventDefault();setOpen(false);return;}
  if(event.key!=='Tab')return;
  const items=[...sidebar.querySelectorAll('a[href],button:not([disabled])')].filter(el=>el.getClientRects().length);
  const first=items[0],last=items[items.length-1];
  if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus();}
  else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus();}
 });
 mobile.addEventListener('change',()=>{const inside=sidebar.contains(document.activeElement);setOpen(false,false);if(mobile.matches&&inside)toggle.focus();});
 setOpen(false,false);
 document.querySelectorAll('.admin-content table.admin-table').forEach(table=>{
  if(table.closest('.table-responsive'))return;
  const wrapper=document.createElement('div');wrapper.className='table-responsive';table.before(wrapper);wrapper.append(table);
 });
})();
