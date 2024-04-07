/*function filtrar() {
  var nomeEmpre = document.getElementById("nomeEmpre");
  var sE = nomeEmpre.selectedIndex;
  var nomeProf = document.getElementById("nomeProf");
  var sP = nomeProf.selectedIndex;

  var options = document.getElementById("nomeServ").options;
  var profissional=[];var comercio =[];
  for (var i = 0; i < options.length; i++) {
    var option = options[i];
      var classes = option.className.split('-C-');
      if (classes.length > 1) {
      var profissional = classes[0];//P
      var CTVI = classes[1];//CTVI
      var classesCTVI = CTVI.split('-T-');
      var comercio = classesCTVI[0];//C

    }else{
      var classes = option.className.split('-T-');//PTVI
      var profissional = classes[0];//P
    }

    option.style.display = "none";

    if (
      (profissional === nomeProf.options[sP].className || nomeProf.options[sP].className === "0-C-0") &&
      (comercio === nomeEmpre.options[sE].className || nomeEmpre.options[sE].className === "0-C-0")
    ) {
      option.style.display = "";
    }
  }
}*/

/*
function CalcTempoServico() {
  const iServico = document.getElementById("nomeServ");
  var sS = iServico.selectedIndex;
  var Ttempo = iServico.options[sS].className.split('-T-');
  var tempo = Ttempo[1];
  var slot = Math.ceil(tempo / 15);
  return slot;
}

function horarios() {
  const mins = ['00', '15', '30', '45'];
  var hora = 8;
  var horarios = [];

  for (var h = 0; h < 15; h++) {
    for (var m = 0; m < 4; m++) {
      var horario = (hora < 10 ? '0' : '') + hora + ':' + mins[m];
      horarios.push(horario);

      if (hora === 22) {
        break;
      }
    }
    hora += 1;
  }
  return horarios;
}

function populaTabela() {
  const iServico = document.getElementById("nomeServ");
  var sS = iServico.selectedIndex;
  console.log(sS);
  // Obtem o texto dentro do option selecionado
  var ts = iServico.options[sS].text.split(' (');
  var textoServico =ts[0];

  // Verifica se um serviço está selecionado
  if (sS < 1) {
    return;
  }

  var Tvalor = iServico.options[sS].className.split('-V-');
  var Tvalor2 = Tvalor[1];
  var Tvalor3 = Tvalor2.split('-I-');
  var valor   = Tvalor3[0];
  var id      = Tvalor3[1];


  var options = document.getElementById("nomeServ").options;
  var profissional=[];
  for (var i = 0; i < options.length; i++) {
    var option = options[i];
      var classes = option.className.split('-C-');
      if (classes.length > 1) {
      var profissional = classes[0];//P
    }else{
      var classes = option.className.split('-T-');//PTVI
      var profissional = classes[0];//P
    }
  }
  const detalhesEvento = document.getElementById("detalhesEvento");

  const agendados = [0, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 1];
  const horariosDisponiveis = horarios();
  var slot = CalcTempoServico(sS);
  var vaga = [];
  var cont = 0;
  var horaVaga = 0;
  var txtSup = '';

  for (let p = 0; p < horariosDisponiveis.length; p++) {
   /* console.log(profissional);
    console.log(valor);
    console.log(textoServico);*/
   /*

    if (agendados[p] === 0) {
      horaVaga = p;
    }

    if (cont == slot) {

      console.log('tem vaga');
      var vg = vaga.length;
      if (vg % 2 == 0) {
        txtSup += '<tr>';
      } else {
        txtSup += '<th></th>';
      }

      vaga[vg] = p;
      txtSup +=
        `
            <th>${horariosDisponiveis[horaVaga]}</th>
            <th id="disponibilidade">Disponivel</th>
            <th id="profissional">${nomeProf}</th>
            <th id="valorServ">${valor}</th>
            <th>
                <a
                    href="{{URL}}/user/agendar/${p}/Agendar"><button
                    type="button" class="btn btn-primary btn-sm">Agendar</button>
                </a>
            </th>
        `;

      if (vg % 2 != 0) {
        txtSup += '</tr>';
      }
    }
    cont++;
  }
 console.log(txtSup);
  detalhesEvento.innerHTML = txtSup;
}


// Aciona a função populaTabela no evento onchange do input de data
document.getElementById("dataEvento").addEventListener("change", populaTabela);

// ...

// Popula a tabela ao carregar a página
populaTabela();*/