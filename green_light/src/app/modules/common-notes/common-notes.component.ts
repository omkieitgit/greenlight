import { Component, OnInit,Input, Output, EventEmitter } from '@angular/core';
import {CommonNotesDialogComponent} from './common-notes-dialog/common-notes-dialog.component';
import {MatDialog} from '@angular/material/dialog';
import { Router,ActivatedRoute }    from '@angular/router';
import { CommonApplicationService, AlertService } from '../../shared/_services';
import { apiUrl,commonNotes } from '../../config/api-url';
import { StorageService } from '../../shared/_services/storage.service';
import { Subject } from "rxjs";

@Component({
  selector: 'app-common-notes',
  templateUrl: './common-notes.component.html',
  styleUrls: ['./common-notes.component.css']
})
export class CommonNotesComponent implements OnInit {

  @Input() note_name;
  @Input() noteSetting:any;
  @Output() updateNotesDetail=new EventEmitter<any>();
  @Input() resetFormSubject: Subject<boolean> = new Subject<boolean>();
  @Input() hoaLienSubject: Subject<boolean> = new Subject<boolean>();

  noteBtn:boolean=false;
  noteDetail:boolean=false;
  noteType:any;
  id:string;

  title:string;
  note_info:any;
  property_id: string;
  common_notes:any=[];
  userRole:any;
  loading:boolean=false;
  user_info:any;
  expand:boolean=false;
  notes_index:number;
  cma_arv_type:string='';

  constructor(private dialog:MatDialog,
              private route: ActivatedRoute,
              private commonApplicationService: CommonApplicationService,
              private storageService:StorageService,
              private alertService:AlertService) { }
  

  ngOnInit() {

    if(this.noteSetting){
      this.noteBtn=this.noteSetting.noteBtn?this.noteSetting.noteBtn:'';
      this.noteDetail=this.noteSetting.noteDetail?this.noteSetting.noteDetail:'';
      this.noteType=this.noteSetting.lien_type?this.noteSetting.lien_type:'';
      this.cma_arv_type=this.noteSetting.cma_arv_type?this.noteSetting.cma_arv_type:'';
    }else{
      this.noteDetail=true;
      this.noteBtn=true;
    }
    
    
    this.note_info=commonNotes[this.note_name];
    this.user_info=this.storageService.get("user_info");
    this.userRole = this.user_info['current_role'];
    this.property_id = this.route.parent.snapshot.parent.params.property_id;
    if(!this.property_id){
      this.property_id=this.route.snapshot.paramMap.get('property_id');
    }
    this.title=this.note_info.title;

    this.noteType=this.noteType?this.noteType:this.note_info.note_type?this.note_info.note_type:'';

    if(this.property_id && this.noteDetail){
      this.get_notes();
    }

    this.resetFormSubject.subscribe(response => {
      if(response){
        this.get_notes();
      }
    });
    this.hoaLienSubject.subscribe(response => {
      if(response){
        this.get_notes();
      }
    });
    
  }
  

  get_notes(){
    
    var url =this.note_info.url+'/'+this.property_id;
    if(this.noteType!="")
    {
       url = this.note_info.url+'/'+this.noteType+'/'+this.property_id;
    }
    if(this.noteSetting && this.noteSetting.lien_type){
      url = this.note_info.url+'/'+this.property_id+'/'+this.noteType;
    }
    
    this.commonApplicationService.get(url).subscribe(response => {
      if(response !== undefined && response.data.length>0){             
          this.common_notes=response.data;
      }
    });
  }

  get_date(date){
    if(date){
      var d = new Date((date*1000));
      var back_date = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear()+' '+d.getHours()+':'+d.getMinutes();
      return back_date;
    }
  }

  addNotes(){
    var data={'property_id':this.property_id,
              'url':this.note_info.url,
              'note_name':this.note_info.title,
              'note_type':this.noteType,
              //'lien_type':this.lien_type?this.lien_type:''
              }

    let dialogRef =this.dialog.open(CommonNotesDialogComponent,{ width: '600px',data:data,disableClose:true} );
    dialogRef.afterClosed().subscribe(result => {
      if(result){
        this.common_notes.push(result);
        if(this.noteSetting){
          this.updateNotesDetail.emit(true);
        }
      }
    });
  }

  deleteNotes(id,index){
    this.loading=true;
    var url =this.note_info.url+'/'+id;
    this.commonApplicationService.delete(url).subscribe(response => {
        if(response.status=="success"){
          this.common_notes.splice(index, 1);
          this.alertService.success(response.message);
        }else{
          this.alertService.error(response.message);
        }
        this.loading=false;
    });
  }

  editNotes(id,index){

    let editNote=this.common_notes.filter(note => note.id === id);
    console.log(editNote);
    var data={'property_id':this.property_id,
              'url':this.note_info.url,
              'note_name':this.note_info.title,
              'note_type':this.noteType,
              'info':editNote
              }

    let dialogRef =this.dialog.open(CommonNotesDialogComponent,{ width: '600px',data:data,disableClose:true} );
    dialogRef.afterClosed().subscribe(result => {
      if(result){
        this.common_notes[index]=result;
        if(this.noteSetting){
          this.updateNotesDetail.emit(true);
        }
      }
    });
  }


  toggleList(index){
      this.notes_index=index;
  }

}
