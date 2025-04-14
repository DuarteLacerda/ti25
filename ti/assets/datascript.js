document.addEventListener("DOMContentLoaded", () => {
  async function atualizarSensores() {
    try {
      const resposta = await fetch("api/data.php");
      const dados = await resposta.json();

      for (const sensor in dados) {
        const valorSpan = document.getElementById(`valor-${sensor}`);
        const horaSpan = document.getElementById(`hora-${sensor}`);
        const statuSpan = document.getElementById(`status-${sensor}`);

        if (valorSpan && horaSpan && statuSpan) {
          let valorBruto = dados[sensor].valor.trim(); // valor original
          let valorNum = parseFloat(valorBruto); // para comparação
          let valorFormatado = valorBruto; // para mostrar

          const valorSpanTable = document.querySelector(
            `#tabela-sensores td span#valor-${sensor}`
          );
          const horaSpanTable = document.querySelector(
            `#tabela-sensores td span#hora-${sensor}`
          );

          console.log(
            "Sensor:",
            sensor,
            "Elementos:",
            valorSpan,
            horaSpan,
            statuSpan
          ); // Adicionado para depuração

          switch (sensor) {
            case "led":
              const estado = valorBruto == "1" ? "Ligado" : "Desligado";
              valorFormatado = estado;
              switch (estado) {
                case "Ligado":
                  statuSpan.innerHTML =
                    "<span class='badge bg-success'>Ligado</span>";
                  break;
                case "Desligado":
                  statuSpan.innerHTML =
                    "<span class='badge bg-primary'>Alto</span>";
                  break;
              }
              break;
            case "temperatura":
              valorFormatado = `${valorBruto}ºC`;
              if (valorNum > 40) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Muito Alta</span>";
              } else if (valorNum > 30 && valorNum < 40) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Alta</span>";
              } else if (valorNum > 20 && valorNum < 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Normal</span>";
              } else if (valorNum > 10 && valorNum < 20) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixa</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Muito Baixa</span>";
              }
              break;
            case "humidade":
              valorFormatado = `${valorBruto}%`;
              if (valorNum > 89) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Alta</span>";
              } else if (valorNum < 90 && valorNum > 39) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Normal</span>";
              } else if (valorNum < 40 && valorNum > 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixa</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              }
              break;
            case "cancela":
              valorFormatado = valorBruto == "50" ? "Aberta" : "Fechada";
              if (valorNum >= 140 && valorNum <= 180) {
                // Cancela fechada (em torno de 160)
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Fechada</span>";
              } else if (valorNum >= 30 && valorNum <= 70) {
                // Cancela aberta (em torno de 50)
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Aberta</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Estado indefinido</span>";
              }
              break;
            case "distancia":
              valorFormatado = `${valorBruto} cm`;
              if (valorNum < 11 && valorNum > 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Perto</span>";
              } else if (valorNum > 10 && valorNum < 20) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>+/- Perto</span>";
              } else if (valorNum > 20 && valorNum < 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-warning text-dark'>Longe</span>";
              } else if (valorNum > 30) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Muito Longe</span>";
              } else if (valorNum < 0) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Erro no sensor</span>";
              }
              break;
            case "ventoinha":
              valorFormatado = `${valorBruto} RPM`;
              if (valorNum > 2000) {
                statuSpan.innerHTML =
                  "<span class='badge bg-danger'>Alto</span>";
              } else if (valorNum < 1000) {
                statuSpan.innerHTML =
                  "<span class='badge bg-primary'>Baixo</span>";
              } else {
                statuSpan.innerHTML =
                  "<span class='badge bg-success'>Normal</span>";
              }
              break;
          }

          valorSpan.textContent = ` ${valorFormatado}`;
          horaSpan.textContent = dados[sensor].hora;
          valorSpanTable.textContent = ` ${valorBruto}`;
          horaSpanTable.textContent = dados[sensor].hora;
        }
      }
    } catch (erro) {
      console.error("Erro ao carregar os dados dos sensores:", erro);
    }
  }

  const urlParams = new URLSearchParams(window.location.search);
  const nomeSensor = urlParams.get("nome");

  async function carregarHistorico() {
    try {
      const resposta = await fetch(`api/logs.php?sensor=${nomeSensor}`);
      const dados = await resposta.json();

      const logTexto = dados[nomeSensor]?.log;
      if (!logTexto) return;

      const linhas = logTexto
        .trim()
        .split("\n")
        .filter((l) => l.trim() !== "");
      const tbody = document.querySelector(`#historico-${nomeSensor} tbody`);
      tbody.innerHTML = "";

      linhas.reverse().forEach((linha) => {
        const [dataHora, valor] = linha.split(";");
        const tr = document.createElement("tr");

        // Aqui podes adaptar os alertas com base no tipo de sensor
        let alerta = "";
        const valorNum = parseFloat(valor);

        switch (nomeSensor) {
          case "temperatura":
            if (valorNum > 40) {
              alerta = "<span class='badge bg-danger'>Muito Alta</span>";
            } else if (valorNum < 40 && valorNum > 30) {
              alerta = "<span class='badge bg-warning text-dark'>Alta</span>";
            } else if (valorNum < 30 && valorNum > 20) {
              alerta = "<span class='badge bg-success'>Normal</span>";
            } else if (valorNum < 20 && valorNum > 10) {
              alerta = "<span class='badge bg-primary'>Baixa</span>";
            } else if (valorNum < 10) {
              alerta = "<span class='badge bg-danger'>Muito Baixa</span>";
            }
            break;
          case "humidade":
            if (valorNum > 89) {
              alerta = "<span class='badge bg-danger'>Alta</span>";
            } else if (valorNum < 90 && valorNum > 39) {
              alerta = "<span class='badge bg-warning text-dark'>Normal</span>";
            } else if (valorNum < 40 && valorNum > 0) {
              alerta = "<span class='badge bg-primary'>Baixa</span>";
            } else if (valorNum < 0) {
              alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
            }
            break;
          case "distancia":
            if (valorNum < 11 && valorNum > 0) {
              alerta = "<span class='badge bg-success'>Perto</span>";
            } else if (valorNum > 10 && valorNum < 20) {
              alerta =
                "<span class='badge bg-warning text-dark'>+/- Perto</span>";
            } else if (valorNum > 20 && valorNum < 30) {
              alerta = "<span class='badge bg-warning text-dark'>Longe</span>";
            } else if (valorNum > 30) {
              alerta = "<span class='badge bg-primary'>Muito Longe</span>";
            } else if (valorNum < 0) {
              alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
            }
            break;
          case "cancela":
            if (valorNum > 50) {
              alerta = "<span class='badge bg-success'>Fechada</span>";
            } else if (valorNum < 51) {
              alerta = "<span class='badge bg-danger'>Aberta</span>";
            } else {
              alerta = "<span class='badge bg-success'>Erro na cancela</span>";
            }
            break;
          case "ventoinha":
            if (valorNum > 2000) {
              alerta = "<span class='badge bg-danger'>Alto</span>";
            } else if (valorNum < 1000) {
              alerta = "<span class='badge bg-primary'>Baixo</span>";
            } else {
              alerta = "<span class='badge bg-success'>Normal</span>";
            }
            break;
          case "led":
            if (valorNum == "1") {
              alerta = "<span class='badge bg-success'>Ligado</span>";
            } else if (valorNum == "0") {
              alerta = "<span class='badge bg-primary'>Desligado</span>";
            } else {
              alerta = "<span class='badge bg-danger'>Erro no sensor</span>";
            }
            break;
          default:
            alerta = "<span class='badge bg-secondary'>Desconhecido</span>";
            break;
        }

        tr.innerHTML = `
          <td>${valor}</td>
          <td>${dataHora}</td>
          <td>${alerta}</td>
        `;
        tbody.appendChild(tr);
      });
    } catch (erro) {
      console.error("Erro ao carregar o histórico:", erro);
    }
  }

  setInterval(carregarHistorico, 10);
  setInterval(atualizarSensores, 10);
});
